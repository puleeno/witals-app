<?php

declare(strict_types=1);

namespace App\Foundation\Database;

use Cycle\Database\DatabaseInterface;
use Cycle\Database\DatabaseManager;
use Cycle\Database\DatabaseProviderInterface;
use Psr\Log\LoggerInterface;

/**
 * Database Manager Proxy
 * Handles lazy connection initialization and SQL mode configuration
 */
class DatabaseManagerProxy implements DatabaseProviderInterface
{
    protected DatabaseManager $manager;
    protected array $initializedDatabases = [];
    protected string $defaultDriver;

    public function __construct(
        DatabaseManager $manager,
        string $defaultDriver = 'mysql',
        private readonly ?LoggerInterface $logger = null,
    ) {
        $this->manager = $manager;
        $this->defaultDriver = $defaultDriver;
    }

    public function database(?string $database = null): DatabaseInterface
    {
        $database = $database ?? $this->defaultDriver;
        
        // Get the database instance
        $db = $this->manager->database($database);
        
        // Initialize SQL mode only once per database
        if (!isset($this->initializedDatabases[$database])) {
            $this->initializeSqlMode($db, $database);
            $this->initializedDatabases[$database] = true;
        }
        
        return $db;
    }

    /**
     * Initialize SQL mode for MySQL/MariaDB databases
     * This fixes WordPress legacy tables with zero dates (0000-00-00 00:00:00)
     */
    protected function initializeSqlMode(DatabaseInterface $db, string $driver): void
    {
        if ($driver === 'mysql' || $driver === 'mariadb') {
            try {
                $db->execute("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
                $db->execute("SET sql_mode=(SELECT REPLACE(@@sql_mode,'NO_ZERO_DATE',''))");
                $db->execute("SET sql_mode=(SELECT REPLACE(@@sql_mode,'NO_ZERO_IN_DATE',''))");
                $db->execute("SET sql_mode=(SELECT REPLACE(@@sql_mode,'STRICT_TRANS_TABLES',''))");
                $db->execute("SET sql_mode=(SELECT REPLACE(@@sql_mode,'STRICT_ALL_TABLES',''))");
            } catch (\Throwable $e) {
                if ($this->logger) {
                    $this->logger->warning('Failed to configure SQL mode: {message}', [
                        'message' => $e->getMessage(),
                        'exception' => $e,
                    ]);
                }
            }
        }
    }

    /**
     * Disconnect all database connections
     * Called after each request in long-running environments
     */
    public function disconnect(): void
    {
        try {
            // Access the protected drivers property via reflection
            $reflection = new \ReflectionClass($this->manager);
            $property = $reflection->getProperty('drivers');
            $property->setAccessible(true);
            $drivers = $property->getValue($this->manager);
            
            // Disconnect each driver
            foreach ($drivers as $driver) {
                if (method_exists($driver, 'disconnect')) {
                    $driver->disconnect();
                }
            }
            
            // Reset initialization tracking
            $this->initializedDatabases = [];
        } catch (\Throwable $e) {
            if ($this->logger) {
                $this->logger->warning('Failed to disconnect database: {message}', [
                    'message' => $e->getMessage(),
                    'exception' => $e,
                ]);
            }
        }
    }

    /**
     * Delegate all other methods to the underlying manager
     */
    public function __call(string $method, array $args)
    {
        return $this->manager->$method(...$args);
    }
}
