<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Sevenspan\CodeGenerator\Library\Helper;
use Sevenspan\CodeGenerator\Traits\FileManager;
use Sevenspan\CodeGenerator\Enums\CodeGeneratorFileType;

class MakeController extends Command
{
    use FileManager;

    private const INDENT = '    ';

    protected $signature = 'code-generator:controller {model : The name of the model to associate with the controller} 
                                                     {--methods= : Comma-separated list of methods to include in the controller}  
                                                     {--service : Include a service file for the controller} 
                                                     {--resource : Include resource files for the controller} 
                                                     {--request : Include request files for the controller}
                                                     {--overwrite : is overwriting this file is selected}
                                                     {--adminCrud : is adminCRUD added}';

    protected $description = 'Generate a custom controller with optional methods and service injection';

    public function handle(): void
    {
        $modelName = $this->argument('model');
        $isAdminCrudIncluded = (bool) $this->option('adminCrud');

        // generate the normal controller
        $this->generateController($modelName, isAdminCrudIncluded: false);

        // if admin crud is selected, generate the admin controller
        if ($isAdminCrudIncluded) {
            $this->generateController($modelName, isAdminCrudIncluded: true);
        }
    }

    protected function generateController(string $modelName, bool $isAdminCrudIncluded = false): void
    {
        $controllerClassName = Str::studly($modelName) . 'Controller';

        $controllerPath = $isAdminCrudIncluded
            ? config("code-generator.paths.custom.admin_controller")
            : config("code-generator.paths.default.controller");

        $fullPath = base_path("{$controllerPath}/{$controllerClassName}.php");
        File::ensureDirectoryExists(dirname($fullPath));

        // Generate content
        $content = $this->getReplacedContent($controllerClassName, $isAdminCrudIncluded);

        // Save controller file
        $this->saveFile($fullPath, $content, CodeGeneratorFileType::CONTROLLER);

        $this->appendApiRoute($controllerClassName, $isAdminCrudIncluded);
    }

    /**
     * Get the replaced content for the controller file.
     */
    public function getReplacedContent(string $controllerClassName, bool $isAdminCrudIncluded = false): string
    {
        return $this->getStubContents(
            $this->getStubVariables($controllerClassName, $isAdminCrudIncluded),
            $isAdminCrudIncluded
        );
    }

    /**
     * Get the variables to replace in the stub file.
     */
    public function getStubVariables(string $controllerClassName, bool $isAdminCrudIncluded = false): array
    {
        $modelName = $this->argument('model') ? ucfirst($this->argument('model')) : '';

        return [
            'namespace' => Helper::convertPathToNamespace(
                $isAdminCrudIncluded
                    ? config('code-generator.paths.custom.admin_controller')
                    : config('code-generator.paths.default.controller')
            ),
            'class' => Str::studly($modelName),
            'className' => $controllerClassName,
            'relatedModelNamespace' => "use " . Helper::convertPathToNamespace(config('code-generator.paths.default.model')) . '\\' . $modelName,
            'modelName' => $modelName,  // used in generating methods
        ];
    }

    /**
     * Inject additional use statements into the controller file.
     */
    protected function injectUseStatements(
        string $mainContent,
        bool $includeServiceFile,
        bool $includeRequestFile,
        bool $includeResourceFile,
        string $className,
        bool $isAdminCrudIncluded
    ): string {
        $additionalUseStatements = [];

        // Add service file use statement
        $mainContent = str_replace(
            '{{ service }}',
            $includeServiceFile ? 'use ' . Helper::convertPathToNamespace(config('code-generator.paths.default.service')) . '\\' . $className . 'Service;' : '',
            $mainContent
        );

        // Add request file use statement
        $mainContent = str_replace(
            '{{ request }}',
            $includeRequestFile
                ? 'use ' . Helper::convertPathToNamespace(config('code-generator.paths.default.request'))
                . ($isAdminCrudIncluded ? '\\Admin' : '')
                . "\\{$className}\\Request as {$className}Request;"
                : '',
            $mainContent
        );

        // Add resource file use statements
        $includeResourceFile ? array_push(
            $additionalUseStatements,
            'use ' . Helper::convertPathToNamespace(config('code-generator.paths.default.resource')) . ($isAdminCrudIncluded ? '\\Admin' : '') . "\\{$className}\\Resource as {$className}Resource;",
            'use ' . Helper::convertPathToNamespace(config('code-generator.paths.default.resource')) . ($isAdminCrudIncluded ? '\\Admin' : '') . "\\{$className}\\Collection as {$className}Collection;"
        ) : '';

        $useInsert = implode(PHP_EOL, $additionalUseStatements);

        return str_replace(
            'use App\Http\Controllers\Controller;',
            'use App\Http\Controllers\Controller;' . PHP_EOL . $useInsert,
            $mainContent
        );
    }

    /**
     * Get the contents of the stub file with replaced variables.
     */
    public function getStubContents(array $stubVariables = [], bool $isAdminCrudIncluded = false): string
    {
        $includeServiceFile = (bool) $this->option('service');
        $includeResourceFile = (bool) $this->option('resource');
        $includeRequestFile = (bool) $this->option('request');
        $mainContent = file_get_contents(__DIR__ . '/../../stubs/controller.stub');

        $className = $stubVariables['class'];
        $modelName = $stubVariables['modelName'];
        $singularInstance = lcfirst($className);
        $singularObj = '$' . $singularInstance . 'Obj';

        $methods = $isAdminCrudIncluded ?  ['index', 'store', 'show', 'update', 'destroy'] : explode(',', $this->option('methods') ?? '');
        // Replace stub variables in base content
        foreach ($stubVariables as $search => $replace) {
            $mainContent = str_replace('{{ ' . $search . ' }}', $replace, $mainContent);
        }

        // Replace service property and constructor

        $mainContent = str_replace(
            '{{ serviceObj }}',
            $includeServiceFile ? 'private ' . $className . 'Service $' . $singularInstance . 'Service' : '',
            $mainContent
        );

        // Conditionally inject use statements
        $mainContent = $this->injectUseStatements(
            $mainContent,
            $includeServiceFile,
            $includeRequestFile,
            $includeResourceFile,
            $className,
            $isAdminCrudIncluded
        );

        // Append methods
        $methodContents = '';
        foreach ($methods as $method) {
            $methodStubPath = __DIR__ . "/../../stubs/controller.{$method}.stub";
            if (! file_exists($methodStubPath)) {
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
                $includeRequestFile
                    ? "{$className}Request \$request,{$classObject}"
                    : "Request \$request,{$classObject}",
                $methodContent
            );

            $methodContent = str_replace('{{ classObject }}', $classObject, $methodContent);

            switch ($method) {
                case 'index':
                    $indexReturn = $includeResourceFile
                        ? "return \$this->collection(new {$className}Collection(\${$pluralVar}));"
                        : "return \$this->success(\${$pluralVar});";

                    $indexBody = "\${$pluralVar} = \$this->{$singularInstance}Service->collection(\$request->all());" . PHP_EOL .
                        self::INDENT . self::INDENT . $indexReturn;

                    $methodContent = str_replace('{{ indexMethod }}', $includeServiceFile ? $indexBody : '', $methodContent);
                    break;

                case 'show':
                    $showBody = $includeServiceFile
                        ? "{$singularObj} = \$this->{$singularInstance}Service->resource(\${$singularInstance});" . PHP_EOL .
                        self::INDENT . self::INDENT . "return \$this->resource(new {$className}Resource({$singularObj}));"
                        : '';

                    $methodContent = str_replace('{{ showMethod }}', $includeServiceFile ? $showBody : '', $methodContent);
                    break;

                case 'store':
                    $validated = $includeRequestFile ? '$request->validated()' : '$request->all()';
                    $storeBody = "\${$singularInstance} = \$this->{$singularInstance}Service->store({$validated});" . PHP_EOL .
                        self::INDENT . self::INDENT . "return \$this->success(\${$singularInstance});";

                    $methodContent = str_replace('{{ storeMethod }}', $includeServiceFile ? $storeBody : '', $methodContent);
                    break;


                case 'update':
                    $validated = $includeRequestFile ? ' $request->validated()' : '$request->all()';
                    $updateBody = "{$singularObj} = \$this->{$singularInstance}Service->update(\${$singularInstance},{$validated});" . PHP_EOL .
                        self::INDENT . self::INDENT . "return \$this->success({$singularObj});";

                    $methodContent = str_replace('{{ updateMethod }}', $includeServiceFile ? $updateBody : '', $methodContent);
                    break;

                case 'destroy':
                    $destroyBody = "\$result = \$this->{$singularInstance}Service->destroy(\${$singularInstance});" . PHP_EOL .
                        self::INDENT . self::INDENT . 'return $this->success($result);';

                    $methodContent = str_replace('{{ destroyMethod }}', $includeServiceFile ? $destroyBody : '', $methodContent);
                    break;
            }

            $methodContents .= PHP_EOL . $methodContent . PHP_EOL;
        }

        return $mainContent . $methodContents . PHP_EOL . '}' . PHP_EOL;
    }

    /**
     * Append API route for the model to the routes file.
     */
    protected function appendApiRoute(string $controllerClassName, bool $isAdminCrudIncluded = false): void
    {
        $resource = Str::plural(Str::kebab($this->argument('model')));
        $methodsArray = explode(',', $this->option('methods') ?? '');
        $methodCount = $isAdminCrudIncluded ? 5 : count($methodsArray);

        $apiPath = base_path($isAdminCrudIncluded ? 'routes/api-admin.php' : 'routes/api.php');
        $stubPath = __DIR__ . '/../../stubs/' . ($isAdminCrudIncluded ? 'api.admin.route.stub' : 'api.routes.stub');
        $pathKey = $isAdminCrudIncluded ? 'admin_controller' : 'controller';
        $controllerNamespace = Helper::convertPathToNamespace(
            $isAdminCrudIncluded
                ? config('code-generator.paths.custom.' . $pathKey)
                : config('code-generator.paths.default.' . $pathKey)
        );

        $fullControllerClass = "{$controllerNamespace}\\{$controllerClassName}";
        $useStatement = "use {$fullControllerClass};";

        $routeType = $methodCount === 5 ? 'apiResource' : 'resource';
        $routeOptions = $methodCount === 5 ? '' : "->only(['" . implode("', '", $methodsArray) . "'])";
        $routeEntry = "Route::{$routeType}('{$resource}', {$controllerClassName}::class){$routeOptions};";

        // Load base content
        $baseContent = file_exists($apiPath)
            ? file_get_contents($apiPath)
            : file_get_contents($stubPath);

        // Add use statement if not already present
        if (!str_contains($baseContent, $useStatement)) {
            $baseContent = preg_replace(
                '/(<\?php\s+use\s+Illuminate\\\Support\\\Facades\\\Route;)/',
                "$1\n$useStatement",
                $baseContent
            );
        }

        // Add route line at the end
        $finalContent = rtrim($baseContent) . PHP_EOL . $routeEntry . PHP_EOL;

        file_put_contents($apiPath, $finalContent);
    }
}
