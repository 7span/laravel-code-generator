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
                                                               {--adminCrud : is admin crud is selected}
                                                               {--overwrite : is overwriting this file is selected}';
    protected $description = 'Generate a resource collection class for a specified model.';

    public function handle()
    {
        $modelName = Str::studly($this->argument('model'));
        $adminCrud = $this->option('adminCrud');
        $this->generateResourceCollection($modelName);
        if ($adminCrud) {
            $this->generateResourceCollection($modelName, true);
        }
    }

    protected function generateResourceCollection(string $relatedModelName, bool $isAdminCrudIncluded = false): void
    {
        $resourceFilePath = base_path(config('code-generator.paths.default.resource') . ($isAdminCrudIncluded ? "/Admin/" : "/") . "{$relatedModelName}" . "/Collection.php");
        File::ensureDirectoryExists(dirname($resourceFilePath));
        $content = $this->getReplacedContent($relatedModelName, $isAdminCrudIncluded);
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
     * @param bool $isAdminCrudIncluded
     * @return array
     */
    protected function getStubVariables($modelName, bool $isAdminCrudIncluded = false): array
    {
        $namespace = config('code-generator.paths.default.resource') . ($isAdminCrudIncluded ? "/Admin/" : "/");
        return [
            'namespace' => Helper::convertPathToNamespace($namespace) . '\\' . $modelName,
        ];
    }

    /**
     * Generate the final content for the resource collection file.
     *
     * @param string $modelName
     * @param bool $isAdminCrudIncluded
     * @return string
     */
    protected function getReplacedContent($modelName, bool $isAdminCrudIncluded = false): string
    {
        $content = file_get_contents(__DIR__ . '/../../stubs/resource-collection.stub');

        $stubVariables = $this->getStubVariables($modelName, $isAdminCrudIncluded);
        foreach ($stubVariables as $search => $replace) {
            $content = str_replace('{{ ' . $search . ' }}', $replace, $content);
        }

        return $content;
    }
}
