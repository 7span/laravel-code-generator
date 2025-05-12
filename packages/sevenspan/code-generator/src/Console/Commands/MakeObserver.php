<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Sevenspan\CodeGenerator\Models\CodeGeneratorFileLogs;

class MakeObserver extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:observer {name} {--model=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate an observer class for a specified model.';
    public function __construct(protected Filesystem $files)
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $message = '';
        $status = "error";
        $name = Str::studly($this->argument('name'));
        $observerFilePath = app_path('Observers/' . $name . '.php');

        $this->createDirectoryIfMissing(dirname($observerFilePath));

        //with stub content replcament 
        $contents = $this->getReplacedContent($name);

        if (! $this->files->exists($observerFilePath)) {
            $this->files->put($observerFilePath, $contents);
            $message = "Obbserver created: {$observerFilePath}";
            $status = "success";
            $this->info($message);
        } else {
            $message = "Observer already exists: {$observerFilePath}";
            $this->warn($message);
        }

        CodeGeneratorFileLogs::create([
            'file_type' => 'Observer',
            'file_path' => $observerFilePath,
            ' status' => $status,
            'message' => $message,
            'created_at' => now(),
        ]);
    }
    protected function getStubPath(): string
    {
        return __DIR__ . '/../../stubs/observer.stub';
    }
    protected function getStubVariables($name): array
    {
        $modelName = $this->option('model');
        return [
            'namespace' => 'App\\Observers',
            'class'     => $name,
            'model' => Str::studly($modelName),
            'modelVariable' => Str::camel($modelName),
        ];
    }

    protected function getStubContents(string $stubPath, array $stubVariables): string
    {
        $content = file_get_contents($stubPath);
        foreach ($stubVariables as $search => $replace) {
            $content = str_replace('{{ ' . $search . ' }}', $replace, $content);
        }
        return $content;
    }

    protected function getReplacedContent($name): string
    {
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables($name));
    }
    protected function createDirectoryIfMissing($path): string
    {
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }
        return $path;
    }
}
//make:observer Test --model=Test