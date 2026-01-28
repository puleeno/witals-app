<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\ServiceProvider;
use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\DatabaseManager;
use Cycle\Database\DatabaseProviderInterface;
use Cycle\ORM\Factory;
use Cycle\ORM\ORM;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Schema as ORMSchema;
use Cycle\ORM\SchemaInterface;
use Cycle\Annotated;
use Cycle\Schema;
use Spiral\Tokenizer\ClassLocator;
use Symfony\Component\Finder\Finder;

class DatabaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // 1. Register Database Manager (DBAL)
        $this->singleton(DatabaseProviderInterface::class, function ($app) {
            $config = new DatabaseConfig($app->config('database'));
            return new DatabaseManager($config);
        });

        // 2. Register ORM
        $this->singleton(ORMInterface::class, function ($app) {
            $dbal = $app->make(DatabaseProviderInterface::class);
            
            // In a real app, we should cache the schema
            $schema = $this->getSchema($app, $dbal);

            return new ORM(
                new Factory($dbal),
                new ORMSchema($schema)
            );
        });
    }

    private function getSchema($app, $dbal): array
    {
        // Simple schema compilation on boot (dev mode)
        // For production, this should be cached
        
        $finder = (new Finder())->files()->in([
            $app->basePath('app/Models'),
        ]);
        
        // Check if modules have models
        if ($app->has(\App\Foundation\Module\ModuleManager::class)) {
            $modules = $app->make(\App\Foundation\Module\ModuleManager::class)->all();
            foreach ($modules as $module) {
                if ($module->isEnabled() && is_dir($module->getPath() . '/src/Models')) {
                    $finder->in($module->getPath() . '/src/Models');
                }
            }
        }

        $classLocator = new ClassLocator($finder);

        $schema = (new Schema\Compiler())->compile(new Schema\Registry($dbal), [
            new Schema\Generator\ResetTables(),             // Re-declared table schemas (test mode)
            new Annotated\Embeddings($classLocator),        // register embeddable entities
            new Annotated\Entities($classLocator),          // register annotated entities
            new Annotated\TableInheritance(),               // register STI/JTI
            new Annotated\MergeColumns(),                   // add @Table column declarations
            new Schema\Generator\GenerateRelations(),       // generate entity relations
            new Schema\Generator\GenerateTypecast(),        // typecast non-string columns
            new Schema\Generator\RenderTables(),            // declare table schemas
            new Schema\Generator\ValidateEntities(),        // make sure all entity schemas are correct
        ]);

        return $schema;
    }
}
