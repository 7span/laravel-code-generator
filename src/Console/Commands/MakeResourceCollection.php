<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Sevenspan\CodeGenerator\Library\Helper;
use Sevenspan\CodeGenerator\Traits\FileManager;
use Sevenspan\CodeGenerator\Enums\CodeGeneratorFileType;

class MakeResourceCollection extends Command
{
    use FileManager;
    protected $signature = 'code-generator:resource-collection {model : The name of the model for the resource collection}
                                                              {--overwrite : is overwriting this file is selected}';
    protected $description = 'Generate a resource collection class for a specified model.';

    public function handle()
    {
        $modelName = Str::studly($this->argument('model'));

        // Define the path for the resource collection file
        $resourceFilePath = base_path(config('code-generator.paths.default.resource') . "/{$modelName}/Collection.php");

        File::ensureDirectoryExists(dirname($resourceFilePath));
        $content = $this->getReplacedContent($modelName);

        // Create or overwrite file and get log the status and message
        $this->saveFile(
            $resourceFilePath,
            $content,
            CodeGeneratorFileType::RESOURCE_COLLECTION
        );
    }

    /**
     * Get the variables to replace in the stub file.
     *
     * @param string $modelName
     * @return array
     */
    protected function getStubVariables($modelName): array
    {
        return [
            'namespace' => Helper::convertPathToNamespace(config('code-generator.paths.default.resource') . "/{$modelName}"),
            'modelName' => $modelName,
            'resourceNamespace' => Helper::convertPathToNamespace(config('code-generator.paths.default.resource')),
        ];
    }

    /**
     * Generate the final content for the resource collection file.
     *
     * @param string $modelName
     * @return string
     */
    protected function getReplacedContent($modelName): string
    {
        $content = file_get_contents(__DIR__ . '/../../stubs/resource-collection.stub');

        $stubVariables = $this->getStubVariables($modelName);
        foreach ($stubVariables as $search => $replace) {
            $content = str_replace('{{ ' . $search . ' }}', $replace, $content);
        }

        return $content;
    }
}
