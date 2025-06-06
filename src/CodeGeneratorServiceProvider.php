<?php

namespace Sevenspan\CodeGenerator;

use Livewire\Livewire;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Sevenspan\CodeGenerator\Http\Livewire\Logs;
use Sevenspan\CodeGenerator\Http\Livewire\Index;
use Sevenspan\CodeGenerator\Http\Livewire\RestApi;

class CodeGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * Bind services, merge configuration, and register commands.
     */
    public function register(): void
    {
        // Merge package config with app config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/code-generator.php',
            'code-generator'
        );

        // Register package artisan commands
        $this->commands([
            \Sevenspan\CodeGenerator\Console\Commands\MakeModel::class,
            \Sevenspan\CodeGenerator\Console\Commands\MakeController::class,
            \Sevenspan\CodeGenerator\Console\Commands\MakeMigration::class,
            \Sevenspan\CodeGenerator\Console\Commands\MakePolicy::class,
            \Sevenspan\CodeGenerator\Console\Commands\MakeObserver::class,
            \Sevenspan\CodeGenerator\Console\Commands\MakeFactory::class,
            \Sevenspan\CodeGenerator\Console\Commands\MakeService::class,
            \Sevenspan\CodeGenerator\Console\Commands\MakeNotification::class,
            \Sevenspan\CodeGenerator\Console\Commands\MakeRequest::class,
            \Sevenspan\CodeGenerator\Console\Commands\MakeResource::class,
            \Sevenspan\CodeGenerator\Console\Commands\MakeResourceCollection::class,
            \Sevenspan\CodeGenerator\Console\Commands\ClearLogs::class,
        ]);
    }


    public function boot(): void
    {
        if (!app()->environment(['local'])) {
            return;
        }

        // Publish views from package
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('code-generator'),
        ], 'code-generator-views');

        // Publish config file
        $this->publishes([
            __DIR__ . '/../config/code-generator.php' => config_path('code-generator.php'),
        ], 'code-generator-config');

        // Publish migration files
        $this->publishes([
            __DIR__ . '/Migrations' => database_path('migrations'),
        ], 'code-generator-migrations');

        // Publish stub files
        $this->publishes([
            __DIR__ . '/stubs' => database_path('stubs'),
        ], 'code-generator-stubs');

        // Load routes from package
        Route::middleware('web')
            ->group(__DIR__ . '/../routes/web.php');

        // Load migrations from package
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');

        // Load views from package
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'code-generator');

        // Register Livewire components
        Livewire::component('code-generator::index', Index::class);
        Livewire::component('code-generator::rest-api', RestApi::class);
        Livewire::component('code-generator::logs', Logs::class);
    }
}
