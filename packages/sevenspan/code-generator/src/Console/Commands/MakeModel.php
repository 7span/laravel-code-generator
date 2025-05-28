<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Sevenspan\CodeGenerator\Traits\FileManager;
use Sevenspan\CodeGenerator\Enums\CodeGeneratorFileType;

class MakeModel extends Command
{
    use FileManager;

    private const INDENT = '    ';

    protected $signature = 'codegenerator:model {model : The name of the model} 
                                                {--fields= : Comma-separated fields (e.g., name,age)} 
                                                {--relations= : Model relationships with their foreign key and local key (e.g., Post:hasMany:user_id:id,User:belongsTo:post_id:id)} 
                                                {--methods= : Comma-separated list of controller methods to generate api routes (e.g., index,show,store,update,destroy)}
                                                {--softDelete : Include soft delete} 
                                                {--factory : if factory file is included}
                                                {--traits= : Comma-separated traits to include in the model}
                                                {--overwrite : is overwriting this file is selected}';

    protected $description = 'Generate a custom Eloquent model with optional fields, relations, soft deletes, and traits.';

    public function __construct(protected Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $modelClass = Str::studly($this->argument('model'));
        $modelFilePath = app_path(config('code_generator.model_path', 'Models') . "/{$modelClass}.php");

        $this->createDirectoryIfMissing(dirname($modelFilePath));
        $content = $this->getReplacedContent($modelClass);

        // Create or overwrite file and get log the status and message
        $this->saveFile(
            $modelFilePath,
            $content,
            CodeGeneratorFileType::MODEL
        );
    }

    /**
     * @return string
     */
    protected function getStubPath(): string
    {
        return __DIR__ . '/../../stubs/model.stub';
    }

    /**
     * Generate the final content for the model file.
     *
     * @param string $modelClass
     * @return string
     */
    protected function getReplacedContent(string $modelClass): string
    {
        $stub = file_get_contents($this->getStubPath());
        $variables = $this->getStubVariables($modelClass);

        foreach ($variables as $key => $value) {
            $stub = str_replace('{{ ' . $key . ' }}', $value, $stub);
        }

        return $stub;
    }

    /**
     * Get the variables to replace in the stub file.
     *
     * @param string $modelClass
     * @return array
     */
    protected function getStubVariables(string $modelClass): array
    {
        $traitInfo = $this->getTraitInfo();
        $relationMethods = $this->getRelations();
        $relatedModelImports = $this->getRelatedModels();

        return [
            'namespace' => 'App\\' . config('code_generator.model_path', 'Models'),
            'class' => $modelClass,
            'traitNamespaces' => $traitInfo['uses'],
            'traits' => $traitInfo['apply'],
            'relatedModelNamespaces' => !empty($relatedModelImports) ? implode("\n", array_map(fn($model) => "use App\\Models\\$model;", $relatedModelImports)) : "",
            'relation' => $relationMethods,
            'fillableFields' => $this->getFillableFields($this->option('fields')),
            'deletedAt' => $this->option('softDelete') ? "'deleted_at' => 'datetime'," : '',
            'deletedBy' => $this->option('softDelete') ? "'deleted_by'," : ''
        ];
    }

    /**
     * Prepare fillable fields for the model.
     *
     * @param string|null $fieldsOption
     * @return string
     */
    protected function getFillableFields($fieldsOption): string
    {
        $fillableFields = '';
        if ($fieldsOption) {
            $fields = explode(',', $fieldsOption);
            $fieldNames = [];

            foreach ($fields as $field) {
                $fieldName = explode(':', $field)[0];
                $fieldNames[] = "'" . trim($fieldName) . "',";
            }

            $fillableFields = implode(",\n        ", $fieldNames);
        }
        return $fillableFields;
    }

    /**
     * Get trait information for the model.
     *
     * @return array
     */
    protected function getTraitInfo(): array
    {
        $softDeleteIncluded = $this->option('softDelete');
        $isFactoryIncluded = $this->option('factory');

        $traitUseStatements = [];
        $traitNames = [];

        // Add HasFactory trait if factory file is included
        if ($isFactoryIncluded) {
            $traitUseStatements[] = "use Illuminate\\Database\\Eloquent\\Factories\\HasFactory;";
            $traitNames[] = 'HasFactory';
        }

        // Add SoftDeletes trait if soft delete is included
        if ($softDeleteIncluded) {
            $traitUseStatements[] = "use Illuminate\\Database\\Eloquent\\SoftDeletes;";
            $traitNames[] = 'SoftDeletes';
        }

        // Add custom traits if specified
        $customTraits = $this->option('traits');
        if ($customTraits) {
            foreach (explode(',', $customTraits) as $trait) {
                $trait = trim($trait);
                $traitUseStatements[] = "use App\\" . config('code_generator.trait_path', 'Traits') . "\\$trait;";
                $traitNames[] = $trait;
            }
        }

        return [
            'uses' => implode("\n", $traitUseStatements),
            'apply' => empty($traitNames) ? '' : 'use ' . implode(', ', $traitNames) . ';',
        ];
    }

    /**
     * Generate relation methods for the model.
     *
     * @return string
     */
    protected function getRelations(): string
    {
        $relations = $this->option('relations');
        if (!$relations) return '';

        $methods = [];

        foreach (explode(',', $relations) as $relation) {
            if (!str_contains($relation, ':')) continue;

            [$model, $type, $foreignKey, $localKey] = explode(':', $relation);
            $methodName = Str::camel($model);
            $relatedClass = Str::studly($model);

            // Generate the relation method with foreign key and local key
            $methods[] =
                self::INDENT . 'public function ' . $methodName . '()' . PHP_EOL .
                self::INDENT . '{' . PHP_EOL . self::INDENT . self::INDENT . 'return $this->' . $type . '(' . $relatedClass . '::class' . ($foreignKey ? ", '$foreignKey'" : '') . ($localKey ? ", '$localKey'" : '') . ');' . PHP_EOL .
                self::INDENT . '}' . PHP_EOL;
        }

        return rtrim(implode(PHP_EOL, $methods));
    }

    /**
     * Get related models for imports.
     *
     * @return array
     */
    protected function getRelatedModels(): array
    {
        $relations = $this->option('relations');
        if (!$relations) return [];

        $models = [];

        // Extract model names from relations
        foreach (explode(',', $relations) as $relation) {
            if (!str_contains($relation, ':')) continue;
            [$model,] = explode(':', $relation);
            $models[] = Str::studly($model);
        }
        return array_unique($models);
    }

    /**
     * Create the directory if it does not exist.
     *
     * @param string $path
     * @return void
     */
    protected function createDirectoryIfMissing(string $path): void
    {
        if (!$this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0755, true);
        }
    }
}
