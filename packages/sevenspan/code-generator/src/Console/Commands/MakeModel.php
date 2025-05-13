<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Sevenspan\CodeGenerator\Enums\FileGenerationStatus;
use Sevenspan\CodeGenerator\Models\CodeGeneratorFileLog;

class MakeModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'codegenerator:model {name : The name of the model} 
                                                {--fields= : A comma-separated list of fields for the model (e.g., name:string,age:integer).} 
                                                {--relations= : Define model relationships (e.g., User:hasMany,Post:belongsTo).} 
                                                {--softdelete : Include soft delete} 
                                                {--includeAllTraits : Include all predefined traits in the model.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a custom Eloquent model with optional fields, relations, and traits.';

    /**
     * Constructor to initialize the Filesystem dependency.
     *
     * @param Filesystem $files
     */
    public function __construct(protected Filesystem $files)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $logMessage = '';

        // Get the model name from the command argument
        $modelClass = Str::studly($this->argument('name'));
        $modelFilePath = app_path('Models/' . $modelClass . '.php');

        // Create the directory if it doesn't exist
        $this->createDirectoryIfMissing(dirname($modelFilePath));

        // Generate the model content with stub replacements
        $contents = $this->getReplacedContent($modelClass);

        // Check if the model file already exists
        if (! $this->files->exists($modelFilePath)) {
            $this->files->put($modelFilePath, $contents);
            $logMessage = "Model file has been created successfully at: {$modelFilePath}";
            $logStatus = FileGenerationStatus::SUCCESS;
            $this->info($logMessage);

            // Append the API route for the model
            $this->appendApiRoute($modelClass);
        } else {
            $logMessage = "Model file already exists at: {$modelFilePath}";
            $logStatus = FileGenerationStatus::ERROR;
            $this->warn($logMessage);
        }

        // Log the model creation details
        CodeGeneratorFileLog::create([
            'file_type' => 'Model',
            'file_path' => $modelFilePath,
            'status' => $logStatus,
            'message' => $logMessage,
        ]);
    }

    /**
     * Append the API route for the model to the routes/api.php file.
     *
     * @param string $modelName
     * @return void
     */
    protected function appendApiRoute(string $modelName): void
    {
        $controllerName = ucfirst($modelName) . 'Controller';
        $resource = Str::plural(Str::kebab($modelName));
        $apiRouteEntry = "Route::apiResource('$resource', \\App\\Http\\Controllers\\$controllerName::class);";
        $apiRoutesFilePath = base_path('routes/api.php');

        // Create the routes/api.php file if it doesn't exist
        if (! $this->files->exists($apiRoutesFilePath)) {
            $this->files->put($apiRoutesFilePath, "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n\n");
            $this->info("routes/api.php file created.");
        }

        // Append the API route entry to the file
        file_put_contents($apiRoutesFilePath, PHP_EOL . $apiRouteEntry . PHP_EOL, FILE_APPEND);
        $logMessage = "Route added to routes/api.php successfully.";
        $this->info($logMessage);

        // Log the route addition details
        CodeGeneratorFileLog::create([
            'file_type' => 'Route',
            'file_path' => $apiRoutesFilePath,
            'status' => FileGenerationStatus::SUCCESS,
            'message' => $logMessage,
        ]);
    }

    /**
     * Get the path to the model stub file.
     *
     * @return string
     */
    protected function getStubPath(): string
    {
        return __DIR__ . '/../../stubs/model.stub';
    }

    /**
     * Get the variables to replace in the stub file.
     *
     * @param string $modelClass
     * @return array
     */
    protected function getStubVariables($modelClass): array
    {
        $includeSoftDeletes = $this->option('softdelete');
        $includeAllTraits = $this->option('includeAllTraits');
        $fieldDefinitions = $this->option('fields');
        $fillableFields = '';

        // Parse the fields for the fillable property
        if ($fieldDefinitions) {
            $fieldArray = explode(',', $fieldDefinitions);
            $fillableFields = implode(",\n        ", array_map(fn($field) => "'$field'", $fieldArray));
        }

        // Return the variables to replace in the stub file
        return [
            'namespace' => 'App\\Models',
            'class' => $modelClass,
            'softdelete' => $includeSoftDeletes ? 'use Illuminate\Database\Eloquent\SoftDeletes;' : '',
            'uses' => $this->getUses($includeSoftDeletes, $includeAllTraits),
            'relation' => $this->getRelations(),
            'fillableFields' => $fillableFields,
        ];
    }

    /**
     * Get the traits to include in the model.
     *
     * @param bool $includeSoftDeletes
     * @param bool $includeAllTraits
     * @return string
     */
    protected function getUses($includeSoftDeletes, $includeAllTraits): string
    {
        $traits = ['HasFactory'];
        if ($includeSoftDeletes) {
            $traits[] = 'SoftDeletes';
        }
        if ($includeAllTraits) {
            $traits[] = 'ApiResponse,BaseModel,BootModel,PaginationTrait,ResourceFilterable';
        }
        return 'use ' . implode(', ', $traits) . ';';
    }

    /**
     * Get the relation methods for the model.
     *
     * @return string
     */
    protected function getRelations(): string
    {
        $relations = $this->option('relations');
        if (!$relations) return '';

        $relationArray = explode(',', $relations);
        $relationMethods = '';

        // Generate relation methods for each relation
        foreach ($relationArray as $relation) {
            if (!str_contains($relation, ':')) continue;
            [$class, $rel] = explode(':', $relation);
            $rel = Str::camel($rel);
            $modelClass = Str::studly($class);
            $relationMethodName = Str::camel($class);

            $relationMethods .= "public function {$relationMethodName}()\n{\n    return \$this->{$rel}(\\App\\Models\\{$modelClass}::class);\n}";
        }

        return $relationMethods;
    }

    /**
     * Generate the final content for the model file.
     *
     * @param string $modelClass
     * @return string
     */
    protected function getReplacedContent($modelClass): string
    {
        return $this->replaceStubVariables($this->getStubPath(), $this->getStubVariables($modelClass));
    }

    /**
     * Replace the variables in the stub content with actual values.
     *
     * @param string $stubPath
     * @param array $stubVariables
     * @return string
     */
    protected function replaceStubVariables(string $stubPath, array $stubVariables): string
    {
        $modelContent = file_get_contents($stubPath);

        // Replace each variable in the stub content
        foreach ($stubVariables as $search => $replace) {
            $modelContent = str_replace('{{ ' . $search . ' }}', $replace, $modelContent);
        }

        return $modelContent;
    }

    /**
     * Create a directory if it does not already exist.
     *
     * @param string $path
     * @return string
     */
    protected function createDirectoryIfMissing($path): string
    {
        // Create the directory if it doesn't exist
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }
}
