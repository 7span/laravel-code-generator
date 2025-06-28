<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Sevenspan\CodeGenerator\Library\Helper;
use Sevenspan\CodeGenerator\Traits\FileManager;
use Sevenspan\CodeGenerator\Enums\CodeGeneratorFileType;

class MakeObserver extends Command
{
    use FileManager;

    protected $signature = 'code-generator:observer {model : The related model for the observer.}
                                                   {--overwrite}';

    protected $description = 'Generate an observer class for a specified model.';

    public function handle()
    {
        $observerClass = Str::studly($this->argument('model')) . "Observer";

        // Define the path for the observer file
        $observerFilePath = base_path(config('code-generator.paths.default.observer') . "/{$observerClass}.php");

        File::ensureDirectoryExists(dirname($observerFilePath));

        $contents = $this->getReplacedContent($observerClass);

        // Create or overwrite file and get log the status and message
        $this->saveFile(
            $observerFilePath,
            $contents,
            CodeGeneratorFileType::OBSERVER
        );
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
            'namespace'              => Helper::convertPathToNamespace(config('code-generator.paths.default.observer')),
            'class'                  => $observerClass,
            'model'                  => $relatedModel,
            'relatedModelNamespace'  => "use " . Helper::convertPathToNamespace(config('code-generator.paths.default.model')) . '\\' . Str::studly($relatedModel),
            'modelInstance'          => Str::camel($relatedModel),
        ];
    }

    /**
     * Get the contents of the stub file with replaced variables.
     *
     * @param array $stubVariables
     * @return string
     */
    protected function getStubContents(array $stubVariables): string
    {
        $content = file_get_contents(__DIR__ . '/../../stubs/observer.stub');

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
        return $this->getStubContents($this->getStubVariables($observerClass));
    }
}
