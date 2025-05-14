<?php

namespace sevenspan\CodeGenerator;

use Livewire\Livewire;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CodeGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * This method is used to bind services into the container and register
     * any commands or configurations required by the package.
     */
    public function register(): void
    {
        // Merge the package's configuration file with the application's configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../config/code_generator.php',
            'code_generator'
        );

        // Register the artisan commands provided by the package
        $this->commands([
            \Sevenspan\CodeGenerator\Console\Commands\MakeModel::class,
            \Sevenspan\CodeGenerator\Console\Commands\MakeController::class,
            \Sevenspan\CodeGenerator\Console\Commands\MakeMigration::class,
            \Sevenspan\CodeGenerator\Console\Commands\MakePolicy::class,
            \Sevenspan\CodeGenerator\Console\Commands\MakeObserver::class,
            \Sevenspan\CodeGenerator\Console\Commands\MakeFactory::class,
            \Sevenspan\CodeGenerator\Console\Commands\MakeService::class,
            \Sevenspan\CodeGenerator\Console\Commands\MakeNotification::class,
            \Sevenspan\CodeGenerator\Console\Commands\MakeResource::class
        ]);
    }

    /**
     * Bootstrap services.
     *
     * This method is used to perform any actions required to bootstrap the package,
     * such as publishing assets, loading routes, and loading migrations.
     */
    public function boot(): void
    {
        // Define a middleware group for the code generator
        Route::middlewareGroup(
            'codeGeneratorMiddleware',
            config('code_generator.middleware', [])
        );

        // Publish the package's configuration file to the application's config directory
        $this->publishes([
            __DIR__ . '/../config/code_generator.php' => config_path('code_generator.php'),
        ], 'config');

        // Publish the package's migration files to the application's migrations directory
        $this->publishes([
            __DIR__ . '/Migrations' => database_path('migrations'),
        ], 'codegenerator-migrations');

        // Publish the package's stub files to the application's stubs directory
        $this->publishes([
            __DIR__ . '/stubs' => database_path('stubs'),
        ], 'stubs');

        // Load the package's routes from the web.php file
        $this->loadRoutesFrom(__DIR__ . "/../routes/web.php");

        // Load the package's migrations from the Migrations directory
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
    }
}
