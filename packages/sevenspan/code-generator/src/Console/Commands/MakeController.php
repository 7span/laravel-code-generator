<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Sevenspan\CodeGenerator\Traits\FileManager;
use Sevenspan\CodeGenerator\Models\CodeGeneratorFileLog;
use Sevenspan\CodeGenerator\Enums\CodeGeneratorFileType;
use Sevenspan\CodeGenerator\Enums\CodeGeneratorFileLogStatus;

class MakeController extends Command
{
    use FileManager;

    private const INDENT = '    ';

    protected $signature = 'codegenerator:controller {modelName : The name of the model to associate with the controller} 
                                                     {--methods= : Comma-separated list of methods to include in the controller}  
                                                     {--service : Include a service file for the controller} 
                                                     {--resource : Include resource files for the controller} 
                                                     {--request : Include request files for the controller}
                                                     {--overwrite : is overwriting this file is selected}';

    protected $description = 'Generate a custom controller with optional methods and service injection';

    public function __construct(protected Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $controllerClassName = Str::studly($this->argument('modelName')) . 'Controller';

        // Define the controller file path
        $controllerFilePath = app_path(config('code_generator.controller_path', 'Http/Controllers') . "/{$controllerClassName}.php");
        $this->createDirectoryIfMissing(dirname($controllerFilePath));

        $content = $this->getReplacedContent($controllerClassName);

        // Create or overwrite file and get log the status and message
        $this->saveFile(
            $controllerFilePath,
            $content,
            CodeGeneratorFileType::CONTROLLER
        );
        // append the route to the api routes file
        $this->appendApiRoute($controllerClassName);
    }

    /**
     * @return string
     */
    protected function getStubPath(): string
    {
        return __DIR__ . '/../../stubs/controller.stub';
    }

    /**
     * Get the replaced content for the controller file.
     *
     * @param  string  $controllerClassName
     * @return string
     */
    public function getReplacedContent(string $controllerClassName): string
    {
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables($controllerClassName));
    }

    /**
     * Get the variables to replace in the stub file.
     *
     * @param  string  $controllerClassName
     * @return array
     */
    public function getStubVariables(string $controllerClassName): array
    {
        $modelName = $this->argument('modelName') ? ucfirst($this->argument('modelName')) : '';

        return [
            'namespace' => config('code_generator.controller_path', 'Http\Controllers'),
            'class' => preg_replace('/Controller.*$/i', '', ucfirst($controllerClassName)),
            'className' => $controllerClassName,
            'relatedModelNamespace' => "use App\\" . config('code_generator.model_path', 'Models') . "\\" . $modelName,
            'modelName' => $modelName,  // used in generating methods
        ];
    }

    /**
     * Inject additional use statements into the controller file.
     *
     * @param  string  $mainContent
     * @param  bool  $includeServiceFile
     * @param  bool  $includeRequestFile
     * @param  bool  $includeResourceFile
     * @param  string  $className
     * @return string
     */
    protected function injectUseStatements(
        string $mainContent,
        bool $includeServiceFile = false,
        bool $includeRequestFile = false,
        bool $includeResourceFile = false,
        string $className
    ): string {
        $additionalUseStatements = [];

        // Add service file use statement
        if ($includeServiceFile) {
            $mainContent = str_replace(
                '{{ service }}',
                'use App\\' . config('code_generator.service_path', 'Services') . '\\' . $className . 'Service;',
                $mainContent
            );
        }

        // Add request file use statement
        if ($includeRequestFile) {
            $mainContent = str_replace(
                '{{ request }}',
                "use App\\" . config('code_generator.request_path', 'Http\Requests') . "\\{$className}\\Request as {$className}Request;",
                $mainContent
            );
        }

        // Add resource file use statements
        if ($includeResourceFile) {
            $additionalUseStatements[] = "use App\\" . config('code_generator.resource_path', 'Http\Resources') . "\\{$className}\\Resource;";
            $additionalUseStatements[] = "use App\\" . config('code_generator.resource_path', 'Http\Resources') . "\\{$className}\\Collection;";
        }

        $useInsert = implode(PHP_EOL, $additionalUseStatements);

        return str_replace(
            'use App\Http\Controllers\Controller;',
            'use App\Http\Controllers\Controller;' . PHP_EOL . $useInsert,
            $mainContent
        );
    }

    /**
     * Get the contents of the stub file with replaced variables.
     *
     * @param  string  $mainStub
     * @param  array  $stubVariables
     * @return string
     */
    public function getStubContents(string $mainStub, array $stubVariables = []): string
    {
        $includeServiceFile = (bool) $this->option('service');
        $includeResourceFile = (bool) $this->option('resource');
        $includeRequestFile = (bool) $this->option('request');
        $mainContent = file_get_contents($mainStub);

        $className = $stubVariables['class'];
        $modelName = $stubVariables['modelName'];
        $singularInstance = lcfirst($className);
        $singularObj = '$' . $singularInstance . 'Obj';

        $methods = explode(',', $this->option('methods') ?? '');

        // Replace stub variables in base content
        foreach ($stubVariables as $search => $replace) {
            $mainContent = str_replace('{{ ' . $search . ' }}', $replace, $mainContent);
        }

        // Replace service property and constructor
        $mainContent = str_replace(
            '{{ singularService }}',
            $includeServiceFile ? 'private $' . $singularInstance . 'Service;' : '',
            $mainContent
        );

        $mainContent = str_replace(
            '{{ serviceObj }}',
            $includeServiceFile ? '$this->' . $singularInstance . 'Service = new ' . $className . 'Service;' : '',
            $mainContent
        );

        // Conditionally inject use statements
        $mainContent = $this->injectUseStatements(
            $mainContent,
            $includeServiceFile,
            $includeRequestFile,
            $includeResourceFile,
            $className
        );

        // Append methods
        $methodContents = '';

        foreach ($methods as $method) {
            $methodStubPath = __DIR__ . "/../../stubs/controller.{$method}.stub";
            if (!file_exists($methodStubPath)) {
                continue;
            }

            $methodContent = file_get_contents($methodStubPath);
            $pluralVar = Str::plural($singularInstance);
            $classObject = "{$modelName} \${$singularInstance}";

            // Common replacements
            $methodContent = str_replace(
                '{{ requestName }}',
                $includeRequestFile ? "{$className}Request \$request" : 'Request $request',
                $methodContent
            );

            $methodContent = str_replace(
                '{{ updaterRequestName }}',
                $includeRequestFile ? "{$classObject}, {$className}Request \$request" : $classObject,
                $methodContent
            );

            $methodContent = str_replace('{{ classObject }}', $classObject, $methodContent);

            switch ($method) {
                case 'index':
                    $indexReturn = $includeResourceFile
                        ? "return \$this->collection(new Collection(\${$pluralVar}));"
                        : "return \$this->success(\${$pluralVar});";

                    $indexBody = "\${$pluralVar} = \$this->{$singularInstance}Service->collection(\$request->all());" . PHP_EOL .
                        self::INDENT . self::INDENT . $indexReturn;

                    $methodContent = str_replace('{{ indexMethod }}', $includeServiceFile ? $indexBody : '', $methodContent);
                    break;

                case 'store':
                    $validated = $includeRequestFile ? '$request->validated()' : '';
                    $storeBody = "{$singularObj} = \$this->{$singularInstance}Service->store({$validated});" . PHP_EOL .
                        self::INDENT . self::INDENT . "return \$this->success({$singularObj});";

                    $methodContent = str_replace('{{ storeMethod }}', $includeServiceFile ? $storeBody : '', $methodContent);
                    break;

                case 'show':
                    $id = $singularInstance . '->id';
                    $showBody = $includeServiceFile
                        ? "{$singularObj} = \$this->{$singularInstance}Service->resource(\${$id});" . PHP_EOL .
                        self::INDENT . self::INDENT . "return \$this->resource(new Resource({$singularObj}));"
                        : '';

                    $methodContent = str_replace('{{ showMethod }}', $includeServiceFile ? $showBody : '', $methodContent);
                    break;

                case 'update':
                    $validated = $includeRequestFile ? ' $request->validated()' : '';
                    $updateBody = "{$singularObj} = \$this->{$singularInstance}Service->update(\${$singularInstance},{$validated});" . PHP_EOL .
                        self::INDENT . self::INDENT . "return \$this->success({$singularObj});";

                    $methodContent = str_replace('{{ updateMethod }}', $includeServiceFile ? $updateBody : '', $methodContent);
                    break;

                case 'destroy':
                    $destroyBody = "\$result = \$this->{$singularInstance}Service->destroy(\${$singularInstance}->id);" . PHP_EOL .
                        self::INDENT . self::INDENT . "return \$this->success(\$result);";

                    $methodContent = str_replace('{{ destroyMethod }}', $includeServiceFile ? $destroyBody : '', $methodContent);
                    break;
            }

            $methodContents .= PHP_EOL . $methodContent . PHP_EOL;
        }

        return $mainContent . $methodContents . PHP_EOL . '}' . PHP_EOL;
    }

    /**
     * @param string $path
     */
    protected function createDirectoryIfMissing(string $path): void
    {
        if (!$this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0755, true);
        }
    }


    /**
     * Append API route for the model to the routes/api.php file.
     *
     * @param string $controllerClassName
     * @return void
     */
    protected function appendApiRoute(string $controllerClassName): void
    {
        $methods = $this->option('methods');
        $methodCount = count(array_map('trim', explode(',', $methods)));

        $resource = Str::plural(Str::kebab($this->argument('modelName')));
        // Use apiResource if all 5 standard methods are included, otherwise use resource with only()
        if ($methodCount == 5) {
            $routeEntry = "Route::apiResource('{$resource}', \\App\\" . config('code_generator.controller_path', 'Http\Controllers') . "\\{$controllerClassName}::class);";
        } else {
            $routeEntry = "Route::resource('{$resource}', \\App\\" . config('code_generator.controller_path', 'Http\Controllers') . "\\{$controllerClassName}::class)->only(['{$methods}']);";
        }

        $apiPath = base_path('routes/api.php');

        if (!$this->files->exists($apiPath)) {
            $this->files->put($apiPath, "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n\n");
            $this->info("routes/api.php file created.");
        }

        file_put_contents($apiPath, PHP_EOL . $routeEntry . PHP_EOL, FILE_APPEND);
        $this->info("Route added for {$controllerClassName} at :{$apiPath}");

        CodeGeneratorFileLog::create([
            'file_type' => CodeGeneratorFileType::ROUTE,
            'file_path' => $apiPath,
            'status' => CodeGeneratorFileLogStatus::SUCCESS,
            'message' => "API route added for {$controllerClassName} at :{$apiPath}.",
        ]);
    }
}
