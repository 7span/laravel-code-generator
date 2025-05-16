<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Sevenspan\CodeGenerator\Enums\CodeGeneratorFileType;
use Sevenspan\CodeGenerator\Models\CodeGeneratorFileLog;
use Sevenspan\CodeGenerator\Enums\CodeGeneratorFileLogStatus;
use Sevenspan\CodeGenerator\Traits\ManagesFileCreationAndOverwrite;

class MakeResourceController extends Command
{
    use ManagesFileCreationAndOverwrite;
    protected $signature = 'codegenerator:resource-collection {name : The name of the model for the resource collection}';

    protected $description = 'Generate a resource collection class for a specified model.';

    public function __construct(protected Filesystem $files)
    {
        parent::__construct();
    }

    public function handle()
    {
        $modelName = Str::studly($this->argument('name'));

        // Define the path for the resource collection file
        $resourceFilePath = app_path("Http/" . config('code_generator.resource_path', 'Resources') . "/{$modelName}/Controller.php");

        $this->createDirectoryIfMissing($resourceFilePath);

        $content = $this->getReplacedContent($modelName);

        // Create or overwrite migration file and get the status and message
        [$logStatus, $logMessage, $isOverwrite] = $this->createOrOverwriteFile(
            $resourceFilePath,
            $content,
            'Resource'
        );

        // Log the resource collection creation details
        CodeGeneratorFileLog::create([
            'file_type' => CodeGeneratorFileType::RESOURCE,
            'file_path' => $resourceFilePath,
            'status'    => $logStatus,
            'message'   => $logMessage,
            'is_overwrite' => $isOverwrite,
        ]);
    }

    /**
     * Get the path to the resource collection stub file.
     *
     * @return string
     */
    protected function getStubPath(): string
    {
        // Return the path to the resource collection stub file
        return __DIR__ . '/../../stubs/resource-collection.stub';
    }

    /**
     * Get the variables to replace in the stub file.
     *
     * @param string $modelName
     * @return array
     */
    protected function getStubVariables($modelName): array
    {
        // Return the variables to replace in the stub file
        return [
            'namespace' => "App\\" . config('code_generator.resource_path', 'Resources') . "\\{$modelName}",
            'class'     => 'Collection',
        ];
    }

    /**
     * Generate the final content for the resource collection file.
     *
     * @param string $modelName
     * @return string
     */
    protected function getReplacedContent($modelName): string
    {
        // Get the stub path
        $stubPath = $this->getStubPath();

        // Read the stub file content
        $content = file_get_contents($stubPath);

        // Get the stub variables
        $stubVariables = $this->getStubVariables($modelName);

        // Replace each variable in the stub content
        foreach ($stubVariables as $search => $replace) {
            $content = str_replace('{{ ' . $search . ' }}', $replace, $content);
        }

        return $content;
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
