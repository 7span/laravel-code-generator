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
                                                   {--adminCrud : is admin crud is selected}
                                                   {--overwrite : is overwriting this file is selected}';

    protected $description = 'Generate a resource class for a specified model.';

    public function handle()
    {
        $modelName = Str::studly($this->argument('model'));
        $adminCrud = $this->option('adminCrud');
        $this->generateResource($modelName);
        if ($adminCrud) {
            $this->generateResource($modelName, true);
        }
    }

    protected function generateResource(string $relatedModelName, bool $isAdminCrudIncluded = false): void
    {
        $requestFilePath = base_path(config('code-generator.paths.default.resource') . ($isAdminCrudIncluded ? "/Admin/" : "/") . "{$relatedModelName}" . "/Resource.php");
        File::ensureDirectoryExists(dirname($requestFilePath));
        $content = $this->getReplacedContent($relatedModelName, $isAdminCrudIncluded);
        $this->saveFile(
            $requestFilePath,
            $content,
            CodeGeneratorFileType::REQUEST
        );
    }
    /**
     * Get the variables to replace in the stub file.
     *
     * @param string $modelName
     * @param bool $isAdminCrudIncluded
     * @return array
     */
    protected function getStubVariables($modelName, bool $isAdminCrudIncluded = false): array
    {
        $namespace = config('code-generator.paths.default.resource') . ($isAdminCrudIncluded ? "/Admin/" : "/");
        return [
            'namespace'       => Helper::convertPathToNamespace($namespace) . '\\' . $modelName,
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
     * @param bool $isAdminCrudIncluded
     * @return string
     */
    protected function getReplacedContent($modelName, bool $isAdminCrudIncluded = false): string
    {
        return $this->getStubContents($this->getStubVariables($modelName, $isAdminCrudIncluded));
    }
}
