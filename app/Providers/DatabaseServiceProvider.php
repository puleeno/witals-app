<?php

declare(strict_types=1);

namespace App\Providers;

use Witals\Framework\Database\DatabaseServiceProvider as AbstractDatabaseServiceProvider;
use Psr\Log\LoggerInterface;
use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\DatabaseManager;
use Cycle\Database\DatabaseProviderInterface;
use Cycle\ORM\ORMInterface;
use Symfony\Component\Finder\Finder;

class DatabaseServiceProvider extends AbstractDatabaseServiceProvider
{
    protected function getEntityPaths(): array
    {
        $app = $this->app;
        $paths = [$app->basePath('app/Models')];

        if ($app->has(\App\Foundation\Module\ModuleManager::class)) {
            $modules = $app->make(\App\Foundation\Module\ModuleManager::class)->all();
            foreach ($modules as $module) {
                $moduleModels = $module->getPath() . '/src/Models';
                if ($module->isEnabled() && is_dir($moduleModels)) {
                    $paths[] = $moduleModels;
                }
            }
        }

        if ($app->has('db.entity_paths')) {
            foreach ((array) $app->make('db.entity_paths') as $extra) {
                if (is_dir($extra)) {
                    $paths[] = $extra;
                }
            }
        }

        return $paths;
    }

    public function register(): void
    {
        $this->singleton(\App\Foundation\Database\QueryInterceptor::class, function ($app) {
            return new \App\Foundation\Database\QueryInterceptor($app->make(\App\Foundation\Debug\DebugBar::class));
        });

        $this->singleton(\App\Foundation\Database\ModuleSchemaManager::class, function ($app) {
            return new \App\Foundation\Database\ModuleSchemaManager(
                $app->make(\Cycle\Database\DatabaseProviderInterface::class),
                $app->make(\Psr\Log\LoggerInterface::class),
            );
        });

        $this->singleton(DatabaseProviderInterface::class, function ($app) {
            $dbConfig = $app->config('database');
            if (!is_array($dbConfig) || empty($dbConfig)) {
                $dbConfig = [
                    'default' => env('DB_CONNECTION', 'mysql'),
                    'databases' => ['default' => ['connection' => env('DB_CONNECTION', 'mysql')]],
                    'connections' => [
                        'mysql' => [
                            'driver'  => 'mysql',
                            'host'    => env('DB_HOST', '127.0.0.1'),
                            'port'    => env('DB_PORT', 3306),
                            'dbname'  => env('DB_DATABASE', 'app'),
                            'user'    => env('DB_USERNAME', 'root'),
                            'password' => env('DB_PASSWORD', ''),
                        ],
                        'sqlite' => [
                            'driver'  => 'sqlite',
                            'options' => ['memory' => true],
                        ],
                    ],
                ];
            }
            $driver = $dbConfig['default'] ?? env('DB_CONNECTION', 'mysql');
            $config = new DatabaseConfig($dbConfig);
            $manager = new DatabaseManager($config);

            if (env('APP_DEBUG_BAR', false) && $app->has(\App\Foundation\Debug\DebugBar::class)) {
                $manager->setLogger($app->make(\App\Foundation\Database\QueryInterceptor::class));
            }

            return new \App\Foundation\Database\DatabaseManagerProxy($manager, $driver);
        });

        $this->singleton(\Cycle\Database\DatabaseInterface::class, function ($app) {
            return $app->make(DatabaseProviderInterface::class)->database();
        });

        $this->singleton('wpdb', function ($app) {
            return $app->make(\Cycle\Database\DatabaseInterface::class);
        });

        $this->singleton(ORMInterface::class, function ($app) {
            $dbal = $app->make(DatabaseProviderInterface::class);
            return $this->resolveOrm($app, $dbal);
        });

        $this->singleton(\Cycle\ORM\EntityManagerInterface::class, function ($app) {
            return new \Cycle\ORM\EntityManager($app->make(ORMInterface::class));
        });

        $app = $this->app;
        $this->app->terminating(function () use ($app) {
            if ($app->isLongRunning()) {
                $dbal = $app->make(DatabaseProviderInterface::class);
                if ($dbal instanceof \App\Foundation\Database\DatabaseManagerProxy) {
                    $dbal->disconnect();
                }
            }
        });
    }

    public function boot(): void
    {
        try {
            $app = $this->app;
            if ($app->has(\App\Foundation\Module\ModuleManager::class)) {
                $moduleManager = $app->make(\App\Foundation\Module\ModuleManager::class);
                $schemaManager = $app->make(\App\Foundation\Database\ModuleSchemaManager::class);

                foreach ($moduleManager->allSorted() as $module) {
                    if ($module->isEnabled()) {
                        $synced = $schemaManager->syncModule($module->getPath());
                        if (!empty($synced)) {
                            $app->make(LoggerInterface::class)->info(
                                'SchemaManager: [{module}] synced tables: {tables}',
                                ['module' => $module->getName(), 'tables' => implode(', ', $synced)]
                            );
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            $this->app->make(LoggerInterface::class)->error(
                'SchemaManager boot error: {message} in {file}:{line}',
                ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine(), 'exception' => $e]
            );
        }
    }
}
