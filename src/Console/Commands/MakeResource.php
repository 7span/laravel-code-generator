<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Sevenspan\CodeGenerator\Library\Helper;
use Sevenspan\CodeGenerator\Traits\FileManager;
use Sevenspan\CodeGenerator\Enums\CodeGeneratorFileType;

class MakeResource extends Command
{
    use FileManager;

    protected $signature = 'code-generator:resource {model : The name of the model for the resource}
                                                   {--overwrite : is overwriting this file is selected}';

    protected $description = 'Generate a resource class for a specified model.';

    public function handle()
    {
        $modelName = Str::studly($this->argument('model'));

        // Define the path for the resource file
        $resourceFilePath = base_path(config('code-generator.paths.default.resource') . "/{$modelName}/Resource.php");

        File::ensureDirectoryExists(dirname($resourceFilePath));

        $content = $this->getReplacedContent($modelName);

        // Create or overwrite file and get log the status and message
        $this->saveFile(
            $resourceFilePath,
            $content,
            CodeGeneratorFileType::RESOURCE
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
            'namespace'       => Helper::convertPathToNamespace(config('code-generator.paths.default.resource') . "/{$modelName}"),
            'class'           => 'Resource',
            'modelName'       => $modelName,
            'relatedModelNamespace'  => "use " . Helper::convertPathToNamespace(config('code-generator.paths.default.model') . "/{$modelName}") . ";",
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
        $content = file_get_contents(__DIR__ . '/../../stubs/resource.stub');

        foreach ($stubVariables as $search => $replace) {
            $content = str_replace('{{ ' . $search . ' }}', $replace, $content);
        }

        return $content;
    }

    /**
     * Generate the final content for the resource file.
     *
     * @param string $modelName
     * @return string
     */
    protected function getReplacedContent($modelName): string
    {
        return $this->getStubContents($this->getStubVariables($modelName));
    }
}
