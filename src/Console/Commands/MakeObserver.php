<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Sevenspan\CodeGenerator\Traits\FileManager;
use Sevenspan\CodeGenerator\Enums\CodeGeneratorFileType;


class MakeObserver extends Command
{
    use FileManager;

    protected $signature = 'code-generator:observer {model : The related model for the observer.}
                                                   {--overwrite}';

    protected $description = 'Generate an observer class for a specified model.';

    public function __construct(protected Filesystem $files)
    {
        parent::__construct();
    }

    public function handle()
    {
        $observerClass = Str::studly($this->argument('model')) . "Observer";

        // Define the path for the observer file
        $observerFilePath = base_path(config('code-generator.paths.observer', 'App\Observers') . "/{$observerClass}.php");

        $this->createDirectoryIfMissing(dirname($observerFilePath));

        $contents = $this->getReplacedContent($observerClass);

        // Create or overwrite file and get log the status and message
        $this->saveFile(
            $observerFilePath,
            $contents,
            CodeGeneratorFileType::OBSERVER
        );
    }

    /**
     * @return string
     */
    protected function getStubPath(): string
    {
        return __DIR__ . '/../../stubs/observer.stub';
    }

    /**
     * Get the variables to replace in the stub file.
     *
     * @param string $observerClass
     * @return array
     */
    protected function getStubVariables($observerClass): array
    {
        $relatedModel = $this->argument('model');
        return [
            'namespace'              => config('code-generator.paths.observer', 'App\Observers'),
            'class'                  => $observerClass,
            'model'                  => $relatedModel,
            'relatedModelNamespace'  => "use " . config('code-generator.paths.model', 'App\Models') . '\\' . Str::studly($relatedModel),
            'modelInstance'          => Str::camel($relatedModel),
        ];
    }

    /**
     * Get the contents of the stub file with replaced variables.
     *
     * @param string $stubPath
     * @param array $stubVariables
     * @return string
     */
    protected function getStubContents(string $stubPath, array $stubVariables): string
    {
        $content = file_get_contents($stubPath);

        foreach ($stubVariables as $search => $replace) {
            $content = str_replace('{{ ' . $search . ' }}', $replace, $content);
        }

        return $content;
    }

    /**
     * Generate the final content for the observer file.
     *
     * @param string $name
     * @return string
     */
    protected function getReplacedContent($observerClass): string
    {
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables($observerClass));
    }

    /**
     * @param string $path
     */
    protected function createDirectoryIfMissing($path): void
    {
        // Create the directory if it doesn't exist
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }
    }
}
