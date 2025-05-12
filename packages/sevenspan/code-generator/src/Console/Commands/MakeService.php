<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Sevenspan\CodeGenerator\Models\CodeGeneratorFileLogs;

class MakeService extends Command
{
    const INDENT = '    ';

    protected $help = 'Make a new service class';

    protected $signature = 'make:custom-service {name}';

    protected $description = 'Generate a custom service class';

    public function __construct(protected Filesystem $files)
    {
        parent::__construct();
    }

    public function handle()
    {
        $logMessage = '';
        $logStatus = 'error';

        $serviceName = Str::studly($this->argument('name'));
        $serviceFilePath = app_path("Services/{$serviceName}Service.php");

        $this->createDirectoryIfMissing(dirname($serviceFilePath));

        $stubContent = $this->getReplacedContent($serviceName);

        if (! $this->files->exists($serviceFilePath)) {
            $this->files->put($serviceFilePath, $stubContent);
            $logMessage = "Service created: {$serviceFilePath}";
            $logStatus = 'success';
            $this->info($logMessage);
        } else {
            $logMessage = "Service already exists: {$serviceFilePath}";
            $this->info($logMessage);
        }

        CodeGeneratorFileLogs::create([
            'file_type' => 'Service',
            'file_path' => $serviceFilePath,
            'status' => $logStatus,
            'message' => $logMessage,
            'created_at' => now(),
        ]);
    }

    protected function getStubContents(string $stubPath, array $stubVariables): string
    {
        $content = file_get_contents($stubPath);

        foreach ($stubVariables as $search => $replace) {
            $content = str_replace('{{ ' . $search . ' }}', $replace, $content);
        }

        return $content;
    }

    protected function getReplacedContent(string $serviceName): string
    {
        return $this->getStubContents(
            $this->getStubPath(),
            $this->getStubVariables($serviceName)
        );
    }

    protected function createDirectoryIfMissing(string $path): string
    {
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }

    protected function getStubPath(): string
    {
        return __DIR__ . '/../../stubs/service.stub';
    }

    protected function getStubVariables(string $serviceName): array
    {
        $modelClass = Str::studly($serviceName);
        $modelVariable = Str::camel($serviceName);
        $modelInstance = $modelVariable . 'Model';

        return [
            'modelNamespace'    => "App\\Models\\{$modelClass}",
            'serviceClass'      => "{$modelClass}Service",
            'modelObject'       => "private {$modelClass} \${$modelInstance}",
            'resourceMethod'    => $this->getResourceMethod($modelInstance),
            'collectionMethod'  => $this->getCollectionMethod($modelVariable, $modelInstance),
            'storeMethod'       => $this->getStoreMethod($modelVariable, $modelInstance),
            'updateMethod'      => $this->getUpdateMethod($modelVariable),
            'deleteMethod'      => $this->getDeleteMethod($modelVariable),
        ];
    }

    protected function getResourceMethod(string $modelInstance): string
    {
        $queryVar = '$query';

        return "{$queryVar} = \$this->{$modelInstance}->getQB();" . PHP_EOL .
            self::INDENT . "if (is_numeric(\$id)) {" . PHP_EOL . self::INDENT . self::INDENT . "{$queryVar} = {$queryVar}->whereId(\$id);" . PHP_EOL . self::INDENT . "} else {" . PHP_EOL .
            self::INDENT . self::INDENT . "{$queryVar} = {$queryVar}->whereUuid(\$id);" . PHP_EOL .
            self::INDENT . "}" . PHP_EOL .
            self::INDENT . "return {$queryVar}->firstOrFail();";
    }

    protected function getCollectionMethod(string $modelVar, string $modelInstance): string
    {
        $queryVar = '$query';
        $pluralVar = Str::plural($modelVar);

        return "{$queryVar} = \$this->{$modelInstance}->getQB();" . PHP_EOL .
            self::INDENT . "return (isset(\$inputs['limit']) && \$inputs['limit'] != -1) ? {$queryVar}->paginate(\$inputs['limit']) : {$queryVar}->get();";
    }

    protected function getStoreMethod(string $modelVar, string $modelInstance): string
    {
        $modelVarDollar = '$' . $modelVar;

        return "{$modelVarDollar} = \$this->{$modelInstance}->create(\$inputs);" . PHP_EOL .
            self::INDENT . "return {$modelVarDollar};";
    }

    protected function getUpdateMethod(string $modelVar): string
    {
        $modelVarDollar = '$' . $modelVar;

        return "{$modelVarDollar} = \$this->resource(\$id);" . PHP_EOL .
            self::INDENT . "{$modelVarDollar}->update(\$inputs);" . PHP_EOL .
            self::INDENT . "{$modelVarDollar} = \$this->resource({$modelVarDollar}->id);" . PHP_EOL .
            self::INDENT . "return {$modelVarDollar};";
    }

    protected function getDeleteMethod(string $modelVar): string
    {
        $modelVarDollar = '$' . $modelVar;

        return "{$modelVarDollar} = \$this->resource(\$id, \$inputs);" . PHP_EOL .
            self::INDENT . "{$modelVarDollar}->delete();" . PHP_EOL .
            self::INDENT . "\$data['message'] = __('deleteAccountSuccessMessage');" . PHP_EOL .
            self::INDENT . "return \$data;";
    }
}
