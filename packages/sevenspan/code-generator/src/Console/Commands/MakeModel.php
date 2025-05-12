<?php

namespace Sevenspan\CodeGenerator\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Sevenspan\CodeGenerator\Models\CodeGeneratorFileLogs;

class MakeModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:custom-model {name} {--relations=} {--softdelete} {--includeAllTraits}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'to make model';


    public function __construct(protected Filesystem $files)
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $message = '';
        $status = 'error';
        $className = Str::studly($this->argument('name'));
        $modelPath = app_path('Models/' . $className . '.php');

        //make directory if doesn't exist
        $this->createDirectoryIfMissing(dirname($modelPath));

        //with stub content replcament 
        $contents = $this->getReplacedContent($className);

        if (! $this->files->exists($modelPath)) {
            $this->files->put($modelPath, $contents);
            $message = "Model created: {$modelPath}";
            $status = "success";
            $this->info($message);
            $this->appendApiRoute($className);
        } else {
            $message = "Model already exists: {$modelPath}";
            $this->warn($message);
        }
        CodeGeneratorFileLogs::create([
            'file_type' => 'Model',
            'file_path' => $modelPath,
            ' status' => $status,
            'message' => $message,
            'created_at' => now(),
        ]);
    }
    protected function appendApiRoute(string $modelName): void
    {
        $controllerName = ucfirst($modelName) . 'Controller';
        $resource = Str::plural(Str::kebab($modelName));
        $routeEntry = "Route::apiResource('$resource', \\App\\Http\\Controllers\\$controllerName::class);";
        $apiPath = base_path('routes/api.php');

        if (! $this->files->exists($apiPath)) {
            $this->files->put($apiPath, "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n\n");
            $this->info("Created routes/api.php");
        }

        file_put_contents($apiPath, PHP_EOL . $routeEntry . PHP_EOL, FILE_APPEND);
        $this->info("Route added to routes/api.php");
        CodeGeneratorFileLogs::create([
            'file_type' => 'Route',
            'file_path' => $apiPath,
            ' status' => 'success',
            'message' => " Route added to routes/api.php",
            'created_at' => now(),
        ]);
    }

    protected function getStubPath(): string
    {
        return __DIR__ . '/../../stubs/model.stub';
    }

    protected function getStubVariables($className): array
    {
        $softdelete = $this->option('softdelete');
        $includeAllTraits = $this->option('includeAllTraits');
        return [
            'namespace' => 'App\\Models',
            'class'     => $className,
            'softdelete' => $softdelete ? 'use Illuminate\Database\Eloquent\SoftDeletes;' : '',
            'uses'      => $this->getUses($softdelete, $includeAllTraits),
            'relation'  => $this->getRelations(),
        ];
    }

    protected function getUses($softdelete, $includeAllTraits): string
    {
        $traits = ['HasFactory'];
        if ($softdelete) {
            $traits[] = 'SoftDeletes';
        }
        if ($includeAllTraits) {
            $traits[] = 'ApiResponse,BaseModel,BootModel,PaginationTrait,ResourceFilterable';
        }
        return 'use ' . implode(', ', $traits) . ';';
    }

    protected function getRelations(): string
    {
        $relations = $this->option('relations');
        if (!$relations) return '';

        $relationArray = explode(',', $relations);
        $relationMethods = '';

        foreach ($relationArray as $relation) {
            if (!str_contains($relation, ':')) continue;
            [$class, $rel] = explode(':', $relation);
            $rel = Str::camel($rel);
            $className = Str::studly($class);
            $functionName = Str::camel($class);

            $relationMethods .= "public function {$functionName}()\n{\n    return \$this->{$rel}(\\App\\Models\\{$className}::class);\n}";
        }

        return $relationMethods;
    }

    protected function getReplacedContent($className): string
    {
        return $this->replaceStubVariables($this->getStubPath(), $this->getStubVariables($className));
    }

    protected function replaceStubVariables(string $stubPath, array $stubVariables): string
    {
        $modelContent = file_get_contents($stubPath);

        foreach ($stubVariables as $search => $replace) {
            $modelContent = str_replace('{{ ' . $search . ' }}', $replace, $modelContent);
        }

        return $modelContent;
    }

    protected function createDirectoryIfMissing($path): string
    {
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }
}

//make:model Test --relations='User:hasmany,Post:hasMany,Comment:hasMany' --softdelete --includeAllTraits