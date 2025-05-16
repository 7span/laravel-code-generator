<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Sevenspan\CodeGenerator\Enums\CodeGeneratorFileType;
use Sevenspan\CodeGenerator\Models\CodeGeneratorFileLog;
use Sevenspan\CodeGenerator\Enums\CodeGeneratorFileLogStatus;
use Sevenspan\CodeGenerator\Traits\ManagesFileCreationAndOverwrite;

class MakeModel extends Command
{
    use ManagesFileCreationAndOverwrite;

    private const INDENT = '    ';
    protected $signature = 'codegenerator:model {modelName : The name of the model} 
                                                {--fields= : Comma-separated fields (e.g., name,age)} 
                                                {--relations= : Model relationships (e.g., Post:hasMany,User:belongsTo)} 
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
        $modelClass = Str::studly($this->argument('modelName'));
        $modelFilePath = app_path(config('code_generator.model_path', 'Models') . "/{$modelClass}.php");

        $this->createDirectoryIfMissing(dirname($modelFilePath));
        $content = $this->getReplacedContent($modelClass);

        // Create or overwrite migration file and get the status and message
        [$logStatus, $logMessage, $isOverwrite] = $this->createOrOverwriteFile(
            $modelFilePath,
            $content,
            'Model'
        );

        CodeGeneratorFileLog::create([
            'file_type' => CodeGeneratorFileType::MODEL,
            'file_path' => $modelFilePath,
            'status' => $logStatus,
            'message' => $logMessage,
            'is_overwrite' => $isOverwrite,
        ]);
    }

    /**
     * Append API route for the model to the routes/api.php file.
     *
     * @param string $modelName
     * @return void
     */
    protected function appendApiRoute(string $modelName): void
    {
        $controllerName = "{$modelName}Controller";
        $methods = $this->option('methods');
        $methodCount = count(array_map('trim', explode(',', $methods)));

        $resource = Str::plural(Str::kebab($modelName));
        // Use apiResource if all 5 standard methods are included, otherwise use resource with only()
        if ($methodCount == 5) {
            $routeEntry = "Route::apiResource('{$resource}', \\App\\" . config('code_generator.controller_path', 'Http\Controllers') . "\\{$controllerName}::class);";
        } else {
            $routeEntry = "Route::resource('{$resource}', \\App\\" . config('code_generator.controller_path', 'Http\Controllers') . "\\{$controllerName}::class)->only(['{$methods}']);";
        }

        $apiPath = base_path('routes/api.php');

        if (!$this->files->exists($apiPath)) {
            $this->files->put($apiPath, "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n\n");
            $this->info("routes/api.php file created.");
        }

        file_put_contents($apiPath, PHP_EOL . $routeEntry . PHP_EOL, FILE_APPEND);
        $this->info("Route added for {$modelName}");

        CodeGeneratorFileLog::create([
            'file_type' => CodeGeneratorFileType::ROUTE,
            'file_path' => $apiPath,
            'status' => CodeGeneratorFileLogStatus::SUCCESS,
            'message' => "API route added for {$modelName}.",
        ]);
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
        $fieldsOption = $this->option('fields');
        $relationMethods = $this->getRelations();
        $relatedModelImports = $this->getRelatedModels();

        // Check if deleted_by field exists
        $hasDeletedBy = false;
        if ($fieldsOption) {
            $fields = explode(',', $fieldsOption);
            foreach ($fields as $field) {
                if (trim(explode(':', $field)[0]) === 'deleted_by') {
                    $hasDeletedBy = true;
                    break;
                }
            }
        }

        // Process fillable fields from the fields option
        $fillableFields = '';
        if ($fieldsOption) {
            $fields = explode(',', $fieldsOption);
            $fieldNames = [];

            foreach ($fields as $field) {
                $fieldName = explode(':', $field)[0];
                // Skip deleted_by field
                if (trim($fieldName) !== 'deleted_by') {
                    $fieldNames[] = "'" . trim($fieldName) . "',";
                }
            }

            $fillableFields = implode(",\n        ", $fieldNames);
        }

        return [
            'namespace' => 'App\\' . config('code_generator.model_path', 'Models'),
            'class' => $modelClass,
            'traitNamespaces' => $traitInfo['uses'],
            'traits' => $traitInfo['apply'],
            'relatedModelNamespace' => !empty($relatedModelImports) ? implode("\n", array_map(fn($model) => "use App\\Models\\$model;", $relatedModelImports)) : "",
            'relation' => $relationMethods,
            'fillableFields' => $fillableFields,
            'deletedAt' => $this->option('softDelete') ? "'deleted_at' => 'datetime'," : '',
            'deletedBy' => $hasDeletedBy ? "'deleted_by'," : ''
        ];
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
            'apply' => 'use ' . implode(', ', $traitNames) . ';',
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

            [$model, $type] = explode(':', $relation);
            $methodName = Str::camel($model);
            $relatedClass = Str::studly($model);

            // Generate the relation method
            $methods[] =
                self::INDENT . 'public function ' . $methodName . '()' . PHP_EOL .
                self::INDENT . '{' . PHP_EOL .
                self::INDENT . self::INDENT . 'return $this->' . $type . '(' . $relatedClass . '::class);' . PHP_EOL .
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
