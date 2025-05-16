<?php

namespace Sevenspan\CodeGenerator;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Sevenspan\CodeGenerator\Http\Livewire\Index;
use Sevenspan\CodeGenerator\Http\Livewire\RestApi;
use Sevenspan\CodeGenerator\Console\Commands\MakeRequest;

class CodeGeneratorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }
    public function boot(): void
    { 
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('code-generator'),
        ], 'code-generator-views');
        $this->publishes([
            __DIR__ . '/../config/code_generator.php' => config_path('code_generator.php'),
        ], 'code-generator-config');

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'code-generator');

        Livewire::component('code-generator::index', Index::class);
        Livewire::component('code-generator::rest-api', RestApi::class);

        $this->commands([ MakeRequest::class, ]);
    }
}
