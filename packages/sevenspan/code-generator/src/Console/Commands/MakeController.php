<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Sevenspan\CodeGenerator\Models\CodeGeneratorFileLogs;

class MakeController extends Command
{
    private const INDENT = '    ';
    protected $signature = 'make:custom-controller 
                            {name} 
                            {--methods=} 
                            {--service=} 
                            {--resource=} 
                            {--requestFile=}';

    protected $description = 'Generate a custom controller with optional methods and service injection';

    public function __construct(protected Filesystem $files)
    {
        parent::__construct();
    }
    public function handle()
    {
        $message = '';
        $status = 'error';
        $class = Str::studly($this->argument('name'));
        $controllerPath = app_path("Http/Controllers/Api/{$class}Controller.php");

        $this->createDirectoryIfMissing(dirname($controllerPath));

        $contents = $this->getReplacedContent();

        if (! $this->files->exists($controllerPath)) {
            $this->files->put($controllerPath, $contents);
            $message = "Controller created: {$controllerPath}";
            $status = "success";
            $this->info($message);
        } else {
            $message = "Controller already exists: {$controllerPath}";
            $this->warn($message);
        }

        CodeGeneratorFileLogs::create([
            'file_type' => 'Controller',
            'file_path' => $controllerPath,
            ' status' => $status,
            'message' => $message,
            'created_at' => now(),
        ]);
    }
    protected function getStubPath(): string
    {
        return __DIR__ . '/../../stubs/controller.stub';
    }

    public function getReplacedContent()
    {
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables());
    }
    public function getStubVariables()
    {
        return [
            'namespace' => 'App\\Http\\Controllers\\Api',
            'class' => Str::studly($this->argument('name')),
            'className' => Str::studly($this->argument('name')) . 'Controller',
            'ModelName' => ucfirst($this->argument('name')),
        ];
    }
    protected function injectUseStatements(string $mainContent, string $service, string $requestFile, string $resource, string $className): string
    {
        $extraUses = [];

        if ($service == '1') {
            $mainContent = str_replace('{{ service }}', "use App\Services\\{$className}Service;", $mainContent);
        }

        if ($requestFile == '1') {
            $mainContent = str_replace('{{ request }}', "use App\Http\Request\\{$className}\\Request as {$className}Request;", $mainContent);
        }

        // Resource use statements handled below during method loop
        if ($resource == '1') {
            $extraUses[] = "use App\Http\Resources\\{$className}\\Resource;";
            $extraUses[] = "use App\Http\Resources\\{$className}\\Collection;";
        }

        $useInsert = implode(PHP_EOL, $extraUses);
        return str_replace(
            'use App\Http\Controllers\Controller;',
            'use App\Http\Controllers\Controller;' . PHP_EOL . $useInsert,
            $mainContent
        );
    }
    public function getStubContents($mainStub, $stubVariables = [])
    {
        $service = $this->option('service');
        $resource = $this->option('resource');
        $requestFile = $this->option('requestFile');
        $mainContent = file_get_contents($mainStub);

        $className = $stubVariables['class'];
        $singularInstance  = lcfirst($className);
        $singularObj = '$' . $singularInstance . 'Obj';


        //  method names
        $methods = explode(',', $this->option('methods') ?? '');

        // Replace stub variables in base content
        foreach ($stubVariables as $search => $replace) {
            $mainContent = str_replace('{{ ' . $search . ' }}', $replace, $mainContent);
        }

        // Replace service property and constructor
        $mainContent = str_replace(
            '{{ singularService }}',
            $service == '1' ? 'private $' . $singularInstance . 'Service;' : '',
            $mainContent
        );
        $mainContent = str_replace(
            '{{ serviceObj }}',
            $service == '1' ? '$this->' . $singularInstance . 'Service = new ' . $className . 'Service;' : '',
            $mainContent
        );

        //conditionally use statments for request and resource
        $mainContent = $this->injectUseStatements($mainContent, $service, $requestFile, $resource, $className);

        // Append methods
        $methodContents = '';
        foreach ($methods as $method) {
            $methodStub = __DIR__ . "/../../stubs/controller.{$method}.stub";
            if (!file_exists($methodStub)) continue;

            $methodContent = file_get_contents($methodStub);
            $pluralVar = Str::plural($singularInstance);
            $classObject = "{$className} \${$singularInstance}";


            // Replace placeholders in method stub
            $methodContent = str_replace('{{ requestName }}', $requestFile == '1' ? "{$className}Request \$request" : 'Request $request', $methodContent);
            $methodContent = str_replace('{{ updaterRequestName }}', $requestFile == '1' ? "{$classObject}, {$className}Request \$request" : $classObject, $methodContent);
            $methodContent = str_replace('{{ classObject }}', $classObject, $methodContent);

            // Method-specific logic
            if ($method === 'index') {
                $indexReturn = $resource == '1'
                    ? "return \$this->Collection(new Collection(\${$pluralVar}));"
                    : "return \$this->success(\${$pluralVar});";

                $indexBody = "\${$pluralVar} = \$this->{$singularInstance}Service->collection(\$request->all());" . PHP_EOL .
                    self::INDENT . self::INDENT . $indexReturn;

                $methodContent = str_replace('{{ indexMethod }}', $service == '1' ? $indexBody : '', $methodContent);
            }

            if ($method === 'store') {
                $validated = $requestFile == '1' ? '$request->validated()' : '';
                $storeBody = "{$singularObj} = \$this->{$singularInstance}Service->store({$validated});" . PHP_EOL .
                    self::INDENT . self::INDENT . "return \$this->success({$singularObj});";

                $methodContent = str_replace('{{ storeMethod }}', $service == '1' ? $storeBody : '', $methodContent);
            }

            if ($method === 'show') {
                $id = $singularInstance . '->id';
                $storeBody = $service == '1'
                    ? "{$singularObj} = \$this->{$singularInstance}Service->resource(\${$id});" . PHP_EOL .
                    self::INDENT . self::INDENT . "return \$this->resource(new Resource({$singularObj})) ;"
                    : '';

                $methodContent = str_replace('{{ showMethod }}', $service == '1' ? $storeBody : '', $methodContent);
            }

            if ($method === 'update') {
                $validated = $requestFile == '1' ? ' $request->validated()' : '';
                $updateBody = "{$singularObj} = \$this->{$singularInstance}Service->update(\${$singularInstance},{$validated});" . PHP_EOL .
                    self::INDENT . self::INDENT . "return \$this->success({$singularObj});";

                $methodContent = str_replace('{{ updateMethod }}', $service == '1' ? $updateBody : '', $methodContent);
            }

            if ($method === 'destroy') {
                $destroyBody = "\$result = \$this->{$singularInstance}Service->destroy(\${$singularInstance}->id);" . PHP_EOL .
                    self::INDENT . self::INDENT . "return \$this->success(\$result);";
                $methodContent = str_replace('{{ destroyMethod }}', $service == '1' ? $destroyBody : '', $methodContent);
            }

            $methodContents .= PHP_EOL . $methodContent;
        }
        return $mainContent . $methodContents . PHP_EOL . '}' . PHP_EOL;
    }
    protected function createDirectoryIfMissing(string $path): void
    {
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0755, true);
        }
    }
}

//make:custom-controller  Test  --methods=index,show,store,update,destroy --service=1  --resource=1  --requestFile=1 