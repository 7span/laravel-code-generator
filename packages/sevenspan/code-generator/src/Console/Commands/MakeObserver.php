<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Sevenspan\CodeGenerator\Enums\CodeGeneratorFileType;
use Sevenspan\CodeGenerator\Models\CodeGeneratorFileLog;
use Sevenspan\CodeGenerator\Enums\CodeGeneratorFileLogStatus;

class MakeObserver extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'codegenerator:observer {name : The name of the observer class to generate.} 
                                                   {--model= : The related model for the observer.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate an observer class for a specified model.';

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
        $logStatus = "error";

        // Get the observer class name from the command argument
        $observerClass = Str::studly($this->argument('name'));

        // Define the path for the observer file
        $observerFilePath = app_path(config('code_generator.observer_path', 'Notification') . "/{$observerClass}.php");

        // Create the directory if it doesn't exist
        $this->createDirectoryIfMissing(dirname($observerFilePath));

        // Generate the observer content with stub replacements
        $contents = $this->getReplacedContent($observerClass);

        // Check if the observer file already exists
        if (! $this->files->exists($observerFilePath)) {
            // Create the observer file
            $this->files->put($observerFilePath, $contents);
            $logMessage = "Observer file has been created successfully at: {$observerFilePath}";
            $logStatus = CodeGeneratorFileLogStatus::SUCCESS;
            $this->info($logMessage);
        } else {
            // Log a warning if the observer file already exists
            $logMessage = "Observer file already exists at: {$observerFilePath}";
            $logStatus = CodeGeneratorFileLogStatus::ERROR;
            $this->warn($logMessage);
        }

        // Log the observer creation details
        CodeGeneratorFileLog::create([
            'file_type' => CodeGeneratorFileType::OBSERVER,
            'file_path' => $observerFilePath,
            'status'    => $logStatus,
            'message'   => $logMessage,
        ]);
    }

    /**
     * Get the path to the observer stub file.
     *
     * @return string
     */
    protected function getStubPath(): string
    {
        // Return the path to the observer stub file
        return __DIR__ . '/../../stubs/observer.stub';
    }

    /**
     * Get the variables to replace in the stub file.
     *
     * @param string $observerClass
     * @return array
     */
    protected function getStubVariables($observerClass): array
    {
        // Get the related model name from the --model option
        $relatedModel = $this->option('model');

        // Return the variables to replace in the stub file
        return [
            'namespace'              => 'App\\' . config('code_generator.observer_path', 'Observers'),
            'class'                  => $observerClass,
            'model'                  => $relatedModel,
            'relatedModelNamespace' => config('code_generator.model_path', 'Models') . '\\' . Str::studly($relatedModel),
            'modelInstance'         => Str::camel($relatedModel),
        ];
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
     * Generate the final content for the observer file.
     *
     * @param string $name
     * @return string
     */
    protected function getReplacedContent($observerClass): string
    {
        // Generate the final content by replacing variables in the stub
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables($observerClass));
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
