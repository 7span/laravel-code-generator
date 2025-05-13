<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Sevenspan\CodeGenerator\Enums\FileGenerationStatus;
use Sevenspan\CodeGenerator\Models\CodeGeneratorFileLog;

class MakePolicy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'codegenerator:policy {name} {--model=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a policy class for a specified model.';

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

        // Get the policy class name from the command argument
        $policyClass = Str::studly($this->argument('name'));

        // Define the path for the policy file
        $policyFilePath = app_path('Policies/' . $policyClass . 'Policy.php');

        // Create the directory if it doesn't exist
        $this->createDirectoryIfMissing(dirname($policyFilePath));

        // Generate the policy content with stub replacements
        $contents = $this->getReplacedContent($policyClass);

        // Check if the policy file already exists
        if (! $this->files->exists($policyFilePath)) {
            // Create the policy file
            $this->files->put($policyFilePath, $contents);
            $logMessage = "Policy file has been created successfully at: {$policyFilePath}";
            $logStatus = FileGenerationStatus::SUCCESS;
            $this->info($logMessage);
        } else {
            // Log a warning if the policy file already exists
            $logMessage = "Policy file already exists at: {$policyFilePath}";
            $logStatus = FileGenerationStatus::ERROR;
            $this->warn($logMessage);
        }

        // Log the policy creation details
        CodeGeneratorFileLog::create([
            'file_type' => 'Policy',
            'file_path' => $policyFilePath,
            'status' => $logStatus,
            'message' => $logMessage,
        ]);
    }

    /**
     * Get the path to the policy stub file.
     *
     * @return string
     */
    protected function getStubPath(): string
    {
        // Return the path to the policy stub file
        return __DIR__ . '/../../stubs/policy.stub';
    }

    /**
     * Get the variables to replace in the stub file.
     *
     * @param string $policyClass
     * @return array
     */
    protected function getStubVariables($policyClass): array
    {
        // Get the related model name from the --model option
        $relatedModel = $this->option('model');

        // Return the variables to replace in the stub file
        return [
            'namespace' => 'App\\Policies',
            'class' => $policyClass,
            'model' => Str::studly($relatedModel),
            'modelInstance' => Str::camel($relatedModel),
        ];
    }

    /**
     * Replace the variables in the stub content with actual values.
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
     * Generate the final content for the policy file.
     *
     * @param string $policyClass
     * @return string
     */
    protected function getReplacedContent($policyClass): string
    {
        // Generate the final content by replacing variables in the stub
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables($policyClass));
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
