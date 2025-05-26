<?php

namespace Sevenspan\CodeGenerator;

use Livewire\Livewire;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Sevenspan\CodeGenerator\Http\Livewire\Index;
use Sevenspan\CodeGenerator\Http\Livewire\Logs;
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
            __DIR__ . '/../config/code_generator.php',
            'code_generator'
        );

    }
    public function boot(): void
    {
        // Define middleware group for the code generator routes
        Route::middlewareGroup(
            'codeGeneratorMiddleware',
            config('code_generator.middleware', [])
        );

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('code-generator'),
        ], 'code-generator-views');

        // Publish config file
        $this->publishes([
            __DIR__ . '/../config/code_generator.php' => config_path('code_generator.php'),
        ], 'config');

        // Load routes from package
        $this->loadRoutesFrom(__DIR__ . "/../routes/web.php");

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'code-generator');

        // Livewire componnet Register
        Livewire::component('code-generator::index', Index::class);
        Livewire::component('code-generator::rest-api', RestApi::class);
        Livewire::component('code-generator::logs', Logs::class);
    }
}