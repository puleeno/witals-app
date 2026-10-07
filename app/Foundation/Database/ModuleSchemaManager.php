<?php

declare(strict_types=1);

namespace App\Foundation\Database;

use Cycle\Database\DatabaseProviderInterface;
use Cycle\Database\Schema\AbstractTable;
use Cycle\Database\Schema\AbstractColumn;
use Psr\Log\LoggerInterface;

/**
 * Module Schema Manager
 *
 * Reads schema.json files from each module and synchronises database tables
 * using Cycle DBAL's AbstractTable API. Inspired by Joomla's declarative
 * installer manifest approach — no raw SQL scripts needed.
 *
 * PERFORMANCE: A schema registry table (optilarity_schema_registry) tracks
 * the SHA-256 hash of each module's schema.json. Sync only runs when:
 *   - The hash changed (developer updated schema.json)
 *   - SCHEMA_FORCE_SYNC=true env var is set
 *   - ?refresh_schema query param is present (dev mode)
 *
 * schema.json format:
 * {
 *   "version": "1.0.0",
 *   "tables": [
 *     {
 *       "name": "optilarity_customers",
 *       "columns": [
 *         { "name": "id",         "type": "primary" },
 *         { "name": "email",      "type": "string",  "size": 255, "nullable": false },
 *         { "name": "status",     "type": "enum",    "values": ["active","suspended"], "default": "active" },
 *         { "name": "created_at", "type": "datetime", "nullable": false }
 *       ],
 *       "indexes": [
 *         { "columns": ["email"], "unique": true },
 *         { "name": "idx_status", "columns": ["status"] }
 *       ],
 *       "foreign_keys": [
 *         {
 *           "column":     "customer_id",
 *           "references": "optilarity_customers",
 *           "on":         "id",
 *           "on_delete":  "SET NULL",
 *           "on_update":  "CASCADE"
 *         }
 *       ]
 *     }
 *   ]
 * }
 */
class ModuleSchemaManager
{
    private const REGISTRY_TABLE = 'schema_registry';

    /** Map schema.json type aliases → Cycle DBAL column methods */
    private const TYPE_MAP = [
        'primary'    => 'primary',
        'bigprimary' => 'bigPrimary',
        'integer'    => 'integer',
        'biginteger' => 'bigInteger',
        'tinyint'    => 'tinyInteger',
        'smallint'   => 'smallInteger',
        'string'     => 'string',
        'text'       => 'text',
        'longtext'   => 'longText',
        'mediumtext' => 'mediumText',
        'tinytext'   => 'tinyText',
        'decimal'    => 'decimal',
        'float'      => 'float',
        'double'     => 'float',
        'boolean'    => 'boolean',
        'bool'       => 'boolean',
        'datetime'   => 'datetime',
        'date'       => 'date',
        'time'       => 'time',
        'timestamp'  => 'timestamp',
        'json'       => 'json',
        'binary'     => 'binary',
        'uuid'       => 'uuid',
        'enum'       => 'enum',
    ];

    /** Cache of registry rows already fetched this request */
    private ?array $registryCache = null;

    /** Whether to force sync regardless of hash (set once per process) */
    private bool $forceSync;

    public function __construct(
        private readonly DatabaseProviderInterface $dbal,
        private readonly LoggerInterface $logger,
    ) {
        $this->forceSync = $this->shouldForceSync();
    }

    // =========================================================================
    // Public API
    // =========================================================================

    /**
     * Sync all tables declared in a module's schema.json — only if the schema
     * hash has changed since the last sync (or on force mode).
     *
     * @param string $modulePath Absolute path to the module root directory.
     * @return array  List of table names that were processed, empty if skipped.
     */
    public function syncModule(string $modulePath): array
    {
        $schemaFile = rtrim($modulePath, '/') . '/schema.json';

        if (!file_exists($schemaFile)) {
            return [];
        }

        $raw = file_get_contents($schemaFile);

        if ($raw === false) {
            return [];
        }

        $schema = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->error(
                'ModuleSchemaManager: Invalid JSON in {file}: {error}',
                ['file' => $schemaFile, 'error' => json_last_error_msg()]
            );
            return [];
        }

        $moduleName    = $schema['module']  ?? basename($modulePath);
        $schemaVersion = $schema['version'] ?? '1.0.0';
        
        // Canonicalize the JSON to ensure the hash is consistent regardless of whitespace/formatting
        $currentHash = hash('sha256', json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        // Ensure the registry itself exists (cheap: only runs if table missing)
        $this->ensureRegistry();

        // Skip if this exact hash was already synced (hot path — zero overhead)
        if (!$this->forceSync && $this->isAlreadySynced($moduleName, $currentHash)) {
            return [];
        }

        // Perform the actual table sync
        $processed = [];
        foreach (($schema['tables'] ?? []) as $tableDef) {
            try {
                $this->syncTable($tableDef);
                $processed[] = $tableDef['name'];
            } catch (\Throwable $e) {
                $this->logger->error(
                    "ModuleSchemaManager: Failed to sync table '{table}': {message}",
                    ['table' => $tableDef['name'], 'message' => $e->getMessage(), 'exception' => $e]
                );
            }
        }

        // Record the new hash so we skip on next request
        $this->updateRegistry($moduleName, $schemaVersion, $currentHash);

        return $processed;
    }

    /**
     * Sync a single table definition.
     *
     * Foreign keys are applied AFTER all columns + indexes are saved, so the
     * local column is guaranteed to exist. The referenced table must have been
     * synced already — guaranteed by the topological module load order.
     */
    public function syncTable(array $tableDef): void
    {
        $tableName = $tableDef['name'] ?? null;
        if (!$tableName) {
            throw new \InvalidArgumentException('Table definition must have a "name" field.');
        }

        $db    = $this->dbal->database();
        $table = $db->table($tableName)->getSchema();

        $this->applyColumns($table, $tableDef['columns'] ?? []);
        $this->applyIndexes($table, $tableDef['indexes'] ?? []);
        $this->applyForeignKeys($table, $tableDef['foreign_keys'] ?? []);

        $table->save();
    }

    // =========================================================================
    // Registry (tracks which schema hashes have been applied)
    // =========================================================================

    /**
     * Create the schema registry table if it does not exist.
     * Uses raw DBAL — no schema.json needed, no recursion.
     */
    private function ensureRegistry(): void
    {
        try {
            $db    = $this->dbal->database();
            $table = $db->table(self::REGISTRY_TABLE)->getSchema();

            if ($table->exists()) {
                return;
            }

            $table->column('id')->primary();
            $table->column('module')->string(100)->notNull();
            $table->column('schema_version')->string(20)->notNull()->defaultValue('1.0.0');
            $table->column('schema_hash')->string(64)->notNull();
            $table->column('synced_at')->datetime()->notNull();
            $table->index(['module'])->unique()->setName('uq_schema_module');
            $table->save();

            $this->logger->info('ModuleSchemaManager: Created schema registry table.');
        } catch (\Throwable $e) {
            $this->logger->error('ModuleSchemaManager: Could not ensure registry table: {message}', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);
        }
    }

    /**
     * Check whether the given module+hash combination was already applied.
     * Uses an in-process cache so we only hit the DB once per request.
     */
    private function isAlreadySynced(string $moduleName, string $hash): bool
    {
        if ($this->registryCache === null) {
            $this->loadRegistryCache();
        }

        return isset($this->registryCache[$moduleName])
            && $this->registryCache[$moduleName]['schema_hash'] === $hash;
    }

    /**
     * Load all registry rows into memory (one query per request).
     */
    private function loadRegistryCache(): void
    {
        $this->registryCache = [];

        try {
            $rows = $this->dbal->database()
                ->select('module', 'schema_version', 'schema_hash')
                ->from(self::REGISTRY_TABLE)
                ->fetchAll();

            foreach ($rows as $row) {
                $this->registryCache[$row['module']] = $row;
            }
        } catch (\Throwable) {
            // Registry table may not exist yet — will be created in ensureRegistry()
        }
    }

    /**
     * Upsert a registry row for the given module.
     */
    private function updateRegistry(string $moduleName, string $version, string $hash): void
    {
        try {
            $db  = $this->dbal->database();
            $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

            $exists = $db->select('id')
                ->from(self::REGISTRY_TABLE)
                ->where('module', $moduleName)
                ->fetchAll();

            if (!empty($exists)) {
                $db->update(self::REGISTRY_TABLE, [
                    'schema_version' => $version,
                    'schema_hash'    => $hash,
                    'synced_at'      => $now,
                ], [
                    'module' => $moduleName,
                ]);
            } else {
                $db->insert(self::REGISTRY_TABLE)->values([
                    'module'         => $moduleName,
                    'schema_version' => $version,
                    'schema_hash'    => $hash,
                    'synced_at'      => $now,
                ])->run();
            }

            // Update in-process cache
            if ($this->registryCache !== null) {
                $this->registryCache[$moduleName] = [
                    'module'         => $moduleName,
                    'schema_version' => $version,
                    'schema_hash'    => $hash,
                ];
            }
        } catch (\Throwable $e) {
            $this->logger->error('ModuleSchemaManager: Failed to update registry: {message}', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);
        }
    }

    /**
     * Determine if we should skip the hash check and always run sync.
     * True when: SCHEMA_FORCE_SYNC env is set, or ?refresh_schema param present.
     */
    private function shouldForceSync(): bool
    {
        if (env('SCHEMA_FORCE_SYNC', false)) {
            return true;
        }
        // Same heuristic as ORM schema (see DatabaseServiceProvider)
        if (isset($_GET['refresh_schema'])) {
            return true;
        }
        return false;
    }

    // =========================================================================
    // Columns
    // =========================================================================

    private function applyColumns(AbstractTable $table, array $columns): void
    {
        foreach ($columns as $colDef) {
            $name      = $colDef['name'] ?? null;
            $type      = strtolower($colDef['type'] ?? 'string');
            $cycleType = self::TYPE_MAP[$type] ?? 'string';

            if (!$name) {
                continue;
            }

            $column = $table->column($name);
            $this->applyColumnType($column, $cycleType, $colDef);
            $this->applyColumnOptions($column, $colDef);
        }
    }

    private function applyColumnType(AbstractColumn $column, string $cycleType, array $def): void
    {
        switch ($cycleType) {
            case 'primary':
                $column->primary();
                break;

            case 'bigPrimary':
                $column->bigPrimary();
                break;

            case 'string':
                $column->string((int)($def['size'] ?? 255));
                break;

            case 'decimal':
                $column->decimal((int)($def['precision'] ?? 10), (int)($def['scale'] ?? 2));
                break;

            case 'enum':
                $column->enum($def['values'] ?? ['']);
                break;

            case 'integer':    $column->integer();     break;
            case 'bigInteger': $column->bigInteger();  break;
            case 'tinyInteger':$column->tinyInteger(); break;
            case 'smallInteger':$column->smallInteger();break;
            case 'boolean':    $column->boolean();     break;
            case 'text':       $column->text();        break;
            case 'longText':   $column->longText();    break;
            case 'mediumText': $column->mediumText();  break;
            case 'tinyText':   $column->tinyText();    break;
            case 'datetime':   $column->datetime();    break;
            case 'date':       $column->date();        break;
            case 'time':       $column->time();        break;
            case 'timestamp':  $column->timestamp();   break;
            case 'json':       $column->json();        break;
            case 'float':      $column->float();       break;
            case 'uuid':       $column->uuid();        break;

            case 'binary':
                $column->binary((int)($def['size'] ?? 255));
                break;

            default:
                $column->string();
        }
    }

    private function applyColumnOptions(AbstractColumn $column, array $def): void
    {
        $type = strtolower($def['type'] ?? 'string');

        if (in_array($type, ['primary', 'bigprimary'], true)) {
            return;
        }

        // Nullable
        if (array_key_exists('nullable', $def)) {
            $def['nullable'] ? $column->nullable() : $column->notNull();
        } else {
            $column->notNull();
        }

        // Default value
        if (array_key_exists('default', $def)) {
            $column->defaultValue($def['default']);
        }

        // Unsigned integers
        if (!empty($def['unsigned'])) {
            try { $column->unsigned(); } catch (\Throwable) {}
        }

        // Column comment
        if (!empty($def['comment'])) {
            try { $column->comment($def['comment']); } catch (\Throwable) {}
        }
    }

    // =========================================================================
    // Indexes
    // =========================================================================

    private function applyIndexes(AbstractTable $table, array $indexes): void
    {
        foreach ($indexes as $indexDef) {
            $columns = $indexDef['columns'] ?? [];
            if (empty($columns)) {
                continue;
            }

            $unique = !empty($indexDef['unique']);
            $idx    = $unique ? $table->index($columns)->unique() : $table->index($columns);

            if (!empty($indexDef['name'])) {
                $idx->setName($indexDef['name']);
            }
        }
    }

    // =========================================================================
    // Foreign Keys
    // =========================================================================

    /**
     * Apply foreign key constraints declared in schema.json.
     *
     * Format:
     *   "foreign_keys": [
     *     {
     *       "column"     : "customer_id",
     *       "references" : "optilarity_customers",
     *       "on"         : "id",
     *       "on_delete"  : "SET NULL",   // CASCADE | SET NULL | NO ACTION | RESTRICT
     *       "on_update"  : "CASCADE"
     *     }
     *   ]
     *
     * NOTE: Referenced table is guaranteed to exist because the topological
     * module load order ensures parent module schemas are synced first.
     */
    private function applyForeignKeys(AbstractTable $table, array $foreignKeys): void
    {
        foreach ($foreignKeys as $fkDef) {
            $column     = $fkDef['column']     ?? null;
            $references = $fkDef['references'] ?? null;
            $on         = $fkDef['on']         ?? 'id';

            if (!$column || !$references) {
                continue;
            }

            $onDelete = strtoupper($fkDef['on_delete'] ?? 'NO ACTION');
            $onUpdate = strtoupper($fkDef['on_update'] ?? 'NO ACTION');

            try {
                $table->foreignKey([$column])
                    ->references($references, [$on])
                    ->onDelete($onDelete)
                    ->onUpdate($onUpdate);
            } catch (\Throwable $e) {
                $this->logger->warning(
                    'ModuleSchemaManager: FK {column}->{references}.{on} skipped: {message}',
                    [
                        'column' => $column,
                        'references' => $references,
                        'on' => $on,
                        'message' => $e->getMessage(),
                        'exception' => $e,
                    ]
                );
            }
        }
    }

    // =========================================================================
    // Utilities
    // =========================================================================

    /**
     * Validate a schema.json file and return any errors.
     *
     * @return array List of error strings; empty if valid.
     */
    public function validate(string $schemaFile): array
    {
        $errors = [];

        if (!file_exists($schemaFile)) {
            return ["File not found: {$schemaFile}"];
        }

        $raw    = file_get_contents($schemaFile);
        $schema = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return ["Invalid JSON: " . json_last_error_msg()];
        }

        if (empty($schema['tables'])) {
            $errors[] = "No 'tables' key defined.";
            return $errors;
        }

        foreach ($schema['tables'] as $i => $table) {
            if (empty($table['name'])) {
                $errors[] = "Table #{$i}: missing 'name'.";
            }
            foreach (($table['columns'] ?? []) as $j => $col) {
                if (empty($col['name'])) {
                    $errors[] = "Table '{$table['name']}' column #{$j}: missing 'name'.";
                }
                if (empty($col['type'])) {
                    $errors[] = "Table '{$table['name']}' column '{$col['name']}': missing 'type'.";
                } elseif (!isset(self::TYPE_MAP[strtolower($col['type'])])) {
                    $errors[] = "Table '{$table['name']}' column '{$col['name']}': unknown type '{$col['type']}'.";
                }
            }
        }

        return $errors;
    }

    /**
     * Get the current registry state (for dashboard/debug display).
     */
    public function getRegistryState(): array
    {
        $this->ensureRegistry();
        $this->loadRegistryCache();
        return $this->registryCache ?? [];
    }

    /**
     * Invalidate a module's registry entry, forcing re-sync on next boot.
     */
    public function invalidate(string $moduleName): void
    {
        try {
            $this->dbal->database()
                ->delete(self::REGISTRY_TABLE)
                ->where('module', $moduleName)
                ->run();

            if ($this->registryCache !== null) {
                unset($this->registryCache[$moduleName]);
            }
        } catch (\Throwable $e) {
            $this->logger->error('ModuleSchemaManager: invalidate failed: {message}', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);
        }
    }
}
