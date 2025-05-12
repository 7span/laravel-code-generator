<?php

namespace sevenspan\CodeGenerator;

use Livewire\Livewire;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;


class CodeGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/code_generator.php',
            'code-Generator'
        );

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
     */
    public function boot(): void
    {
        Route::middlewareGroup(
            'codeGenMiddleware',
            config('code_generator.middleware', [])
        );

        $this->publishes([
            __DIR__ . '/../config/code_generator.php' => config_path('code_generator.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/Migrations' => database_path('migrations'),
        ], 'codegen-migrations');

        $this->publishes([
            __DIR__ . '/stubs' => database_path('stubs'),
        ], 'stubs');

        $this->loadRoutesFrom(__DIR__ . "/../routes/web.php");
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
    }
}
