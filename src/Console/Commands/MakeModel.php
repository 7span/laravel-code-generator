<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Sevenspan\CodeGenerator\Library\Helper;
use Sevenspan\CodeGenerator\Traits\FileManager;
use Sevenspan\CodeGenerator\Enums\CodeGeneratorFileType;

class MakeModel extends Command
{
    use FileManager;

    private const INDENT = '    ';

    protected $signature = 'code-generator:model {model : The name of the model} 
                                                {--fields= : Comma-separated fields (e.g., name,age)} 
                                                {--relations=* : Model relationships with their foreign key and local key (e.g., Post:hasMany:user_id:id,User:belongsTo:post_id:id)} 
                                                {--methods= : Comma-separated list of controller methods to generate api routes (e.g., index,show,store,update,destroy)}
                                                {--softDelete : Include soft delete} 
                                                {--factory : if factory file is included}
                                                {--traits= : Comma-separated traits to include in the model}
                                                {--overwrite : is overwriting this file is selected}';

    protected $description = 'Generate a custom Eloquent model with optional fields, relations, soft deletes, and traits.';

    public function handle(): void
    {
        $modelClass = Str::studly($this->argument('model'));
        $modelFilePath = base_path(config('code-generator.paths.default.model')) . "/{$modelClass}.php";
        File::ensureDirectoryExists(dirname($modelFilePath));
        $content = $this->getReplacedContent($modelClass);

        // Create or overwrite file and get log the status and message
        $this->saveFile(
            $modelFilePath,
            $content,
            CodeGeneratorFileType::MODEL
        );
    }

    /**
     * Generate the final content for the model file.
     */
    protected function getReplacedContent(string $modelClass): string
    {
        $stub = file_get_contents(__DIR__ . '/../../stubs/model.stub');
        $variables = $this->getStubVariables($modelClass);

        foreach ($variables as $key => $value) {
            $stub = str_replace('{{ ' . $key . ' }}', $value, $stub);
        }

        return $stub;
    }

    /**
     * Get the variables to replace in the stub file.
     */
    protected function getStubVariables(string $modelClass): array
    {
        $isSoftDeleteIncluded = $this->option('softDelete');
        $hiddenFields = ["'created_at'", "'updated_at'"];
        if ($isSoftDeleteIncluded) {
            $hiddenFields[] = "'deleted_at'";
        }
        $traitInfo = $this->getTraitInfo();
        $relationMethods = $this->getRelations();
        $relatedModelImports = $this->getRelatedModels();

        return [
            'namespace' => Helper::convertPathToNamespace(config('code-generator.paths.default.model')),
            'class' => $modelClass,
            'traitNamespaces' => $traitInfo['uses'],
            'traits' => $traitInfo['apply'],
            'relatedModelNamespaces' => ! empty($relatedModelImports) ? implode("\n", array_map(
                fn($model) => "use " . Helper::convertPathToNamespace(config('code-generator.paths.default.model')) . "\\$model;",
                $relatedModelImports
            )) : '',
            'relation' => $relationMethods,
            'fillableFields' => $this->getFillableFields($this->option('fields')),
            'deletedAt' => $isSoftDeleteIncluded ? "'deleted_at' => 'timestamp'," : '',
            'deletedBy' => $isSoftDeleteIncluded ? "'deleted_by'," : '',
            'hiddenFields' => implode(', ', $hiddenFields),
        ];
    }

    /**
     * Prepare fillable fields for the model.
     *
     * @param  string|null  $fieldsOption
     */
    protected function getFillableFields($fieldsOption): string
    {
        $fillableFields = '';
        if ($fieldsOption) {
            $fields = explode(',', $fieldsOption);
            $fieldNames = [];
            $hasDeletedBy = false;

            foreach ($fields as $field) {
                $fieldName = trim(explode(':', $field)[0]);
                if ($fieldName === 'deleted_at') {
                    continue;
                }
                if ($fieldName === 'deleted_by') {
                    $hasDeletedBy = true;
                    continue;
                }
                if (!in_array($fieldName, ['created_by', 'updated_by'])) {
                    $fieldNames[] = "'" . $fieldName . "',";
                }
            }

            $fieldNames[] = "'created_by',";
            $fieldNames[] = "'updated_by',";
            if ($hasDeletedBy) {
                $fieldNames[] = "'deleted_by',";
            }

            $fillableFields = implode("\n        ", $fieldNames);
        }
        return $fillableFields;
    }

    /**
     * Get trait information for the model.
     */
    protected function getTraitInfo(): array
    {
        $softDeleteIncluded = $this->option('softDelete');
        $isFactoryIncluded = $this->option('factory');

        $traitUseStatements = [];
        $traitNames = [];

        // Add HasFactory trait if factory file is included
        if ($isFactoryIncluded) {
            $traitUseStatements[] = 'use Illuminate\\Database\\Eloquent\\Factories\\HasFactory;';
            $traitNames[] = 'HasFactory';
        }

        // Add SoftDeletes trait if soft delete is included
        if ($softDeleteIncluded) {
            $traitUseStatements[] = 'use Illuminate\\Database\\Eloquent\\SoftDeletes;';
            $traitNames[] = 'SoftDeletes';
        }

        // Add custom traits if specified
        $customTraits = $this->option('traits');
        if ($customTraits) {
            foreach (explode(',', $customTraits) as $trait) {
                $trait = trim($trait);
                $traitUseStatements[] = 'use ' . Helper::convertPathToNamespace(config('code-generator.paths.default.trait')) . "\\$trait;";
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
     */
    protected function getRelations(): string
    {
        $relations = $this->option('relations');
        if (!$relations) {
            return '';
        }

        $methods = [];
        $pluralRelations = [
            'hasMany',
            'belongsToMany',
            'hasManyThrough',
            'morphMany',
            'morphToMany',
        ];

        foreach ($relations as $relation) {
            $relationType = $relation['relation_type'];

            // Use pluaral form of methodName for plural relations (e.g., hasMany, belongsToMany)
            // and singular for singular relations (e.g., hasOne, belongsTo)
            $methodName = in_array($relationType, $pluralRelations) ?
                Str::camel(Str::plural($relation['related_model'])) :
                Str::camel($relation['related_model']);

            $method = 'public function ' . $methodName . '()' . PHP_EOL . self::INDENT . '{' . PHP_EOL . self::INDENT . self::INDENT . 'return $this->' . $relationType . '(';

            if (in_array($relationType, ['hasOneThrough', 'hasManyThrough'])) {
                $args = [
                    $relation['related_model'] . '::class',
                    $relation['intermediate_model'] . '::class',
                    "'{$relation['intermediate_foreign_key']}'",
                    "'{$relation['foreign_key']}'",
                    "'{$relation['local_key']}'",
                    "'{$relation['intermediate_local_key']}'",
                ];
            } else {
                $args = [$relation['related_model'] . '::class'];

                if (! empty($relation['foreign_key'])) {
                    $args[] = "'{$relation['foreign_key']}'";
                }

                if (! empty($relation['local_key'])) {
                    $args[] = "'{$relation['local_key']}'";
                }
            }

            $method .= implode(', ', $args) . ');' . PHP_EOL;
            $method .= self::INDENT . '}' . PHP_EOL;

            $methods[] = $method;
        }

        return rtrim(implode(PHP_EOL . self::INDENT, $methods));
    }

    /**
     * Get related models for imports.
     */
    protected function getRelatedModels(): array
    {
        $relations = $this->option('relations');
        if (!$relations) {
            return [];
        }

        $models = [];

        // Extract model names from relations
        foreach ($relations as $relation) {
            if (!is_array($relation) || empty($relation['related_model'])) {
                continue;
            }
            $models[] = Str::studly($relation['related_model']);
            if (!empty($relation['intermediate_model'])) {
                $models[] = Str::studly($relation['intermediate_model']);
            }
        }

        return array_unique($models);
    }
}
