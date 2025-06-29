<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Sevenspan\CodeGenerator\Library\Helper;
use Sevenspan\CodeGenerator\Traits\FileManager;
use Sevenspan\CodeGenerator\Enums\CodeGeneratorFileType;

class MakeService extends Command
{
    use FileManager;
    const INDENT = '    ';
    protected $signature = 'code-generator:service {model : The name of the service class to generate.}
                                                   {--traits= : List of traits.}
                                                   {--overwrite : is overwriting this file is selected}';
    protected $description = 'Create a new service class with predefined methods for resource';

    public function handle()
    {
        $serviceClass = Str::studly($this->argument('model'));

        // Define the path for the service file
        $serviceFilePath = base_path(config('code-generator.paths.default.service') . "/{$serviceClass}Service.php");

        File::ensureDirectoryExists(dirname($serviceFilePath));

        $content = $this->getReplacedContent($serviceClass);

        // Create or overwrite file and get log the status and message 
        $this->saveFile(
            $serviceFilePath,
            $content,
            CodeGeneratorFileType::SERVICE
        );
    }

    /**
     * Get the contents of the stub file with replaced variables.
     *
     * @param array $stubVariables
     * @return string
     */
    protected function getStubContents(array $stubVariables): string
    {
        $content = file_get_contents(__DIR__ . '/../../stubs/service.stub');
        foreach ($stubVariables as $search => $replace) {
            $content = str_replace('{{ ' . $search . ' }}', $replace, $content);
        }

        return $content;
    }

    /**
     * Generate the final content for the service file.
     *
     * @param string $serviceClass
     * @return string
     */
    protected function getReplacedContent(string $serviceClass): string
    {
        return $this->getStubContents(
            $this->getStubVariables($serviceClass)
        );
    }

    /**
     * Get the variables to replace in the stub file.
     *
     * @param string $serviceClass
     * @return array
     */
    protected function getStubVariables(string $serviceClass): array
    {
        $modelName = Str::studly($serviceClass);
        $modelVariable = Str::camel($serviceClass);
        $modelObj = $modelVariable . 'Obj';

        return [
            'serviceClassNamespace' => Helper::convertPathToNamespace(config('code-generator.paths.default.service')),
            'relatedModelNamespace' => "use " . Helper::convertPathToNamespace(config('code-generator.paths.default.model')) . "\\{$modelName}",
            'traitNameSpaces'       => $this->getTraitNameSpaces(),
            'serviceClass'          => "{$modelName}Service",
            'modelObject'           => "private {$modelName} \${$modelObj}",
            'modelInstance'         => "\$" . $modelVariable,
            'traits' => strpos($this->option('traits'), 'PaginationTrait') !== false
                ? 'use BaseModel, PaginationTrait;'
                : 'use BaseModel;',
            'resourceMethod'        => $this->getResourceMethod($modelObj),
            'collectionMethod'      => $this->getCollectionMethod($modelVariable, $modelObj),
            'storeMethod'           => $this->getStoreMethod($modelVariable, $modelObj),
            'updateMethod'          => $this->getUpdateMethod($modelVariable),
            'deleteMethod'          => $this->getDeleteMethod($modelVariable),
        ];
    }


    /**
     * Get the trait namespaces based on the 'traits' option.
     *
     * @return string
     */
    protected function getTraitNameSpaces(): string
    {
        $traits = $this->option('traits');
        if (!$traits) {
            return '';
        }

        $traitList = array_filter(array_map('trim', explode(',', $traits)));
        if (!$traitList) {
            return '';
        }

        $namespace = Helper::convertPathToNamespace(config('code-generator.paths.default.trait'));
        return implode(PHP_EOL, array_map(fn($trait) => "use {$namespace}\\{$trait};", $traitList));
    }


    /**
     * Generate the resource method for the service.
     *
     * @param string $modelObj
     * @return string
     */
    protected function getResourceMethod(string $modelObj): string
    {
        $modelVar = Str::camel(str_replace('Obj', '', $modelObj));
        $modelVariable = '$' . $modelVar;

        return "{$modelVariable} = \$this->{$modelObj}->findOrFail({$modelVariable}->id);" . PHP_EOL .
            self::INDENT . self::INDENT . "return {$modelVariable};";
    }

    /**
     * Generate the collection method for the service.
     *
     * @param string $modelVar
     * @param string $modelObj
     * @return string
     */
    protected function getCollectionMethod(string $modelVar, string $modelObj): string
    {
        $modelVariable = '$' . $modelVar;

        return "{$modelVariable} = \$this->{$modelObj}->getQB();" . PHP_EOL .
            self::INDENT . self::INDENT . "return \$this->paginationAttribute({$modelVariable});";
    }

    /**
     * Generate the store method for the service.
     *
     * @param string $modelVar
     * @param string $modelObj
     * @return string
     */
    protected function getStoreMethod(string $modelVar, string $modelObj): string
    {
        $modelVariable = '$' . $modelVar;

        return "{$modelVariable} = \$this->{$modelObj}->create(\$inputs);" . PHP_EOL .
            self::INDENT . self::INDENT . "return {$modelVariable};";
    }

    /**
     * Generate the update method for the service.
     *
     * @param string $modelVar
     * @return string
     */
    protected function getUpdateMethod(string $modelVar): string
    {
        $modelVariable = '$' . $modelVar;

        return "{$modelVariable} = \$this->resource({$modelVariable});" . PHP_EOL .
            self::INDENT . self::INDENT . "{$modelVariable}->update(\$inputs);" . PHP_EOL .
            self::INDENT . self::INDENT . "return {$modelVariable};";
    }

    /**
     * Generate the delete method for the service.
     *
     * @param string $modelVar
     * @return string
     */
    protected function getDeleteMethod(string $modelVar): string
    {
        $modelVariable = '$' . $modelVar;

        return "{$modelVariable} = \$this->resource(\$id);" . PHP_EOL .
            self::INDENT . self::INDENT . "return {$modelVariable}->delete();";
    }
}
