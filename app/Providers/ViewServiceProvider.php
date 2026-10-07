<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\ServiceProvider;
use Witals\Framework\Contracts\View\Factory as ViewFactory;
use Witals\Framework\View\Engines\StemplerEngine;
use Witals\Framework\View\Engines\PhpEngine;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register view services.
     */
    public function register(): void
    {
        // The Application class already initializes a ViewManager, 
        // but we can customize it here.
        
        $this->app->extend(ViewFactory::class, function (ViewFactory $view, $app) {
            $cachePath = $app->basePath('storage/framework/views');
            
            // Native PHP engine is already registered by ViewManager
            
            // Register Stempler Engine for .stempler.php and .dark.php files
            $stempler = new StemplerEngine($cachePath, [
                $app->basePath('resources/views'),
            ]);
            
            $view->registerEngine('stempler.php', $stempler);
            $view->registerEngine('dark.php', $stempler);
            
            return $view;
        });
    }

    /**
     * Boot view services.
     */
    public function boot(): void
    {
        // Logic for adding theme paths can go here
        // $themeManager = $this->app->make(ThemeManager::class);
        // if ($theme = $themeManager->getActiveTheme()) {
        //     $this->app->view()->addLocation($theme->getPath() . '/views');
        // }
    }
}
