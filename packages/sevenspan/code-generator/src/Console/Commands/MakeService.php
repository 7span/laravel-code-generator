<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Sevenspan\CodeGenerator\Enums\FileGenerationStatus;
use Sevenspan\CodeGenerator\Models\CodeGeneratorFileLog;

class MakeService extends Command
{
    const INDENT = '    ';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'codegenerator:service {name : The name of the service class to generate.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class with predefined methods for resource';

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

        // Get the service class name from the command argument
        $serviceClass = Str::studly($this->argument('name'));

        // Define the path for the service file
        $serviceFilePath = app_path("Services/{$serviceClass}Service.php");

        // Create the directory if it doesn't exist
        $this->createDirectoryIfMissing(dirname($serviceFilePath));

        // Generate the service content with stub replacements
        $stubContent = $this->getReplacedContent($serviceClass);

        // Check if the service file already exists
        if (! $this->files->exists($serviceFilePath)) {
            // Create the service file
            $this->files->put($serviceFilePath, $stubContent);
            $logMessage = "Service file has been created successfully at: {$serviceFilePath}";
            $logStatus = FileGenerationStatus::SUCCESS;
            $this->info($logMessage);
        } else {
            // Log a warning if the service file already exists
            $logMessage = "Service file already exists at: {$serviceFilePath}";
            $logStatus = FileGenerationStatus::ERROR;
            $this->info($logMessage);
        }

        // Log the service creation details
        CodeGeneratorFileLog::create([
            'file_type' => 'Service',
            'file_path' => $serviceFilePath,
            'status' => $logStatus,
            'message' => $logMessage,
        ]);
    }

    /**
     * Get the contents of the stub file with replaced variables.
     *
     * @param string $stubPath
     * @param array $stubVariables
     * @return string
     */
    protected function getStubContents(string $stubPath, array $stubVariables): string
    {
        // Read the stub file content
        $content = file_get_contents($stubPath);

        // Replace each variable in the stub content
        foreach ($stubVariables as $search => $replace) {
            $content = str_replace('{{ ' . $search . ' }}', $replace, $content);
        }

        return $content;
    }

    /**
     * Generate the final content for the service file.
     *
     * @param string $serviceClass
     * @return string
     */
    protected function getReplacedContent(string $serviceClass): string
    {
        // Generate the final content by replacing variables in the stub
        return $this->getStubContents(
            $this->getStubPath(),
            $this->getStubVariables($serviceClass)
        );
    }

    /**
     * Create a directory if it does not already exist.
     *
     * @param string $path
     * @return string
     */
    protected function createDirectoryIfMissing(string $path): string
    {
        // Create the directory if it doesn't exist
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }

    /**
     * Get the path to the service stub file.
     *
     * @return string
     */
    protected function getStubPath(): string
    {
        // Return the path to the service stub file
        return __DIR__ . '/../../stubs/service.stub';
    }

    /**
     * Get the variables to replace in the stub file.
     *
     * @param string $serviceClass
     * @return array
     */
    protected function getStubVariables(string $serviceClass): array
    {
        // Generate variables for the stub file
        $modelName = Str::studly($serviceClass);
        $modelVariable = Str::camel($serviceClass);
        $modelInstance = $modelVariable . 'Model';

        return [
            'modelNamespace'    => "App\\Models\\{$modelName}",
            'serviceClass'      => "{$modelName}Service",
            'modelObject'       => "private {$modelName} \${$modelInstance}",
            'resourceMethod'    => $this->getResourceMethod($modelInstance),
            'collectionMethod'  => $this->getCollectionMethod($modelVariable, $modelInstance),
            'storeMethod'       => $this->getStoreMethod($modelVariable, $modelInstance),
            'updateMethod'      => $this->getUpdateMethod($modelVariable),
            'deleteMethod'      => $this->getDeleteMethod($modelVariable),
        ];
    }

    /**
     * Generate the resource method for the service.
     *
     * @param string $modelInstance
     * @return string
     */
    protected function getResourceMethod(string $modelInstance): string
    {
        $query = '$query';

        return "{$query} = \$this->{$modelInstance}->getQB();" . PHP_EOL .
            self::INDENT . "if (is_numeric(\$id)) {" . PHP_EOL . self::INDENT . self::INDENT . "{$query} = {$query}->whereId(\$id);" . PHP_EOL . self::INDENT . "} else {" . PHP_EOL .
            self::INDENT . self::INDENT . "{$query} = {$query}->whereUuid(\$id);" . PHP_EOL .
            self::INDENT . "}" . PHP_EOL .
            self::INDENT . "return {$query}->firstOrFail();";
    }

    /**
     * Generate the collection method for the service.
     *
     * @param string $modelVar
     * @param string $modelInstance
     * @return string
     */
    protected function getCollectionMethod(string $modelVar, string $modelInstance): string
    {
        $query = '$query';
        $pluralVar = Str::plural($modelVar);

        return "{$query} = \$this->{$modelInstance}->getQB();" . PHP_EOL .
            self::INDENT . "return (isset(\$inputs['limit']) && \$inputs['limit'] != -1) ? {$query}->paginate(\$inputs['limit']) : {$query}->get();";
    }

    /**
     * Generate the store method for the service.
     *
     * @param string $modelVar
     * @param string $modelInstance
     * @return string
     */
    protected function getStoreMethod(string $modelVar, string $modelInstance): string
    {
        $modelVariable = '$' . $modelVar;

        return "{$modelVariable} = \$this->{$modelInstance}->create(\$inputs);" . PHP_EOL .
            self::INDENT . "return {$modelVariable};";
    }

    /**
     * Generate the update method for the service.
     *
     * @param string $modelVar
     * @return string
     */
    protected function getUpdateMethod(string $modelVar): string
    {
        $modelVariable = '$' . $modelVar;

        return "{$modelVariable} = \$this->resource(\$id);" . PHP_EOL .
            self::INDENT . "{$modelVariable}->update(\$inputs);" . PHP_EOL .
            self::INDENT . "{$modelVariable} = \$this->resource({$modelVariable}->id);" . PHP_EOL .
            self::INDENT . "return {$modelVariable};";
    }

    /**
     * Generate the delete method for the service.
     *
     * @param string $modelVar
     * @return string
     */
    protected function getDeleteMethod(string $modelVar): string
    {
        $modelVariable = '$' . $modelVar;

        return "{$modelVariable} = \$this->resource(\$id, \$inputs);" . PHP_EOL .
            self::INDENT . "{$modelVariable}->delete();" . PHP_EOL .
            self::INDENT . "\$data['message'] = __('deleteAccountSuccessMessage');" . PHP_EOL .
            self::INDENT . "return \$data;";
    }
}
