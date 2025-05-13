<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Sevenspan\CodeGenerator\Enums\FileGenerationStatus;
use Sevenspan\CodeGenerator\Models\CodeGeneratorFileLog;

class MakeController extends Command
{
    // Constant for indentation
    private const INDENT = '    ';

    // Command signature with arguments and options
    protected $signature = 'codegenerator:controller 
                            {className}   
                            {--modelName= : The name of the model to associate with the controller} 
                            {--methods= : Comma-separated list of methods to include in the controller}  
                            {--ServiceFile : Include a service file for the controller} 
                            {--ResourceFile : Include resource files for the controller} 
                            {--RequestFile : Include request files for the controller}';

    // Command description
    protected $description = 'Generate a custom controller with optional methods and service injection';

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
     * Handle the command execution.
     */
    public function handle()
    {
        $logMessage = '';

        /**
         * Get and process the class name
         * className will come as a api\v1\ExampleController
         */
        $className = Str::studly($this->argument('className'));

        // Separate the namespace and controller name
        $seperator = strrpos($className, '\\');

        // Extract the namespace (e.g., api/v1)
        $controllerNamespace = substr($className, 0, $seperator);

        // Extract the class name (e.g., ExampleController)
        $controllerClassName = Str::studly(substr($className, $seperator + 1));

        // Define the controller file path
        $controllerFilePath = app_path("Http/Controllers/Api/V1/{$controllerClassName}.php");

        // Create directory if it doesn't exist
        $this->createDirectoryIfMissing(dirname($controllerFilePath));

        // Generate the controller content
        $contents = $this->getReplacedContent($controllerNamespace, $controllerClassName);

        // Check if the controller already exists
        if (!$this->files->exists($controllerFilePath)) {
            $this->files->put($controllerFilePath, $contents);
            $logMessage = "Controller file has been created successfully at: {$controllerFilePath}";
            $logStatus =  FileGenerationStatus::SUCCESS;
            $this->info($logMessage);
        } else {
            $logMessage = "Controller file already exists at: {$controllerFilePath}";
            $logStatus =  FileGenerationStatus::ERROR;
            $this->warn($logMessage);
        }

        // Log the file creation status
        CodeGeneratorFileLog::create([
            'file_type' => 'Controller',
            'file_path' => $controllerFilePath,
            'status' => $logStatus,
            'message' => $logMessage,
        ]);
    }

    /**
     * Get the path to the stub file.
     *
     * @return string
     */
    protected function getStubPath(): string
    {
        return __DIR__ . '/../../stubs/controller.stub';
    }

    /**
     * Get the replaced content for the controller file.
     *
     * @param string $controllerNamespace
     * @param string $controllerClassName
     * @return string
     */
    public function getReplacedContent($controllerNamespace, $controllerClassName)
    {
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables($controllerNamespace, $controllerClassName));
    }

    /**
     * Get the variables to replace in the stub file.
     *
     * @param string $controllerNamespace
     * @param string $controllerClassName
     * @return array
     */
    public function getStubVariables($controllerNamespace, $controllerClassName)
    {
        return [
            'namespace' => $controllerNamespace,
            'class' => preg_replace('/Controller.*$/i', '', ucfirst($controllerClassName)),
            'className' => $controllerClassName,
            'ModelName' => ucfirst($this->option('modelName')),
        ];
    }

    /**
     * Inject additional use statements into the controller file.
     *
     * @param string $mainContent
     * @param bool $includeServiceFile
     * @param bool $includeRequestFile
     * @param bool $includeResourceFile
     * @param string $className
     * @return string
     */
    protected function injectUseStatements(string $mainContent, bool $includeServiceFile = false, bool $includeRequestFile = false, bool $includeResourceFile = false, string $className): string
    {
        $additionalUseStatements = [];

        // Add service file use statement
        if ($includeServiceFile) {
            $mainContent = str_replace('{{ service }}', "use App\Services\\{$className}Service;", $mainContent);
        }

        // Add request file use statement
        if ($includeRequestFile) {
            $mainContent = str_replace('{{ request }}', "use App\Http\Request\\{$className}\\Request as {$className}Request;", $mainContent);
        }

        // Add resource file use statements
        if ($includeResourceFile) {
            $additionalUseStatements[] = "use App\Http\Resources\\{$className}\\Resource;";
            $additionalUseStatements[] = "use App\Http\Resources\\{$className}\\Collection;";
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
     * @param string $mainStub
     * @param array $stubVariables
     * @return string
     */
    public function getStubContents($mainStub, $stubVariables = [])
    {
        $includeServiceFile = (bool)$this->option('ServiceFile');
        $includeResourceFile = (bool)$this->option('ResourceFile');
        $includeRequestFile = (bool)$this->option('RequestFile');
        $mainContent = file_get_contents($mainStub);

        $className = $stubVariables['class'];
        $modelName = $stubVariables['ModelName'];
        $singularInstance = lcfirst($className);
        $singularObj = '$' . $singularInstance . 'Obj';

        // Method names
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
        $mainContent = $this->injectUseStatements($mainContent, $includeServiceFile, $includeRequestFile, $includeResourceFile, $className);

        // Append methods
        $methodContents = '';
        foreach ($methods as $method) {
            $methodStubPath = __DIR__ . "/../../stubs/controller.{$method}.stub";
            if (!file_exists($methodStubPath)) continue;

            $methodContent = file_get_contents($methodStubPath);
            $pluralVar = Str::plural($singularInstance);
            $classObject = "{$modelName} \${$singularInstance}";

            // Common replacements
            $methodContent = str_replace('{{ requestName }}', $includeRequestFile ? "{$className}Request \$request" : 'Request $request', $methodContent);
            $methodContent = str_replace('{{ updaterRequestName }}', $includeRequestFile ? "{$classObject}, {$className}Request \$request" : $classObject, $methodContent);
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
                    $storeBody = "\${$singularObj} = \$this->{$singularInstance}Service->store({$validated});" . PHP_EOL .
                        self::INDENT . self::INDENT . "return \$this->success(\${$singularObj});";

                    $methodContent = str_replace('{{ storeMethod }}', $includeServiceFile ? $storeBody : '', $methodContent);
                    break;

                case 'show':
                    $id = $singularInstance . '->id';
                    $showBody = $includeServiceFile
                        ? "{$singularObj} = \$this->{$singularInstance}Service->resource(\${$id});" . PHP_EOL .
                        self::INDENT . self::INDENT . "return \$this->resource(new Resource(\${$singularObj}));"
                        : '';

                    $methodContent = str_replace('{{ showMethod }}', $includeServiceFile ? $showBody : '', $methodContent);
                    break;

                case 'update':
                    $validated = $includeRequestFile ? ' $request->validated()' : '';
                    $updateBody = "\${$singularObj} = \$this->{$singularInstance}Service->update(\${$singularInstance},{$validated});" . PHP_EOL .
                        self::INDENT . self::INDENT . "return \$this->success(\${$singularObj});";

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
     * Create a directory if it doesn't exist.
     *
     * @param string $path
     */
    protected function createDirectoryIfMissing(string $path): void
    {
        if (!$this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0755, true);
        }
    }
}
