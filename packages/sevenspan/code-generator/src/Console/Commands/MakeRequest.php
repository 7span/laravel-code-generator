<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Sevenspan\CodeGenerator\Enums\CodeGeneratorFileType;
use Sevenspan\CodeGenerator\Models\CodeGeneratorFileLog;
use Sevenspan\CodeGenerator\Enums\CodeGeneratorFileLogStatus;

class MakeRequest extends Command
{
    // Constant for indentation
    private const INDENT = '    ';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'codegenerator:request {name : The name of the observer class to generate.} 
                                                   {--model= : The related model for the observer.}
                                                   {--rules= :comma seperated list of rules (e.g, Name:required,email:nullable )} ';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a custom form request with validation rules';


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
     */
    public function handle()
    {
        $logMessage = '';
        $logStatus = "error";

        // Get the model name from the command option
        $relatedModelName = Str::studly($this->option('model'));

        // Define the path for the request file
        $requestFilePath = app_path(config('code_generator.request_path', 'Requests') . "/{$relatedModelName}" . "/Request.php");

        // Create the directory if it doesn't exist
        $this->createDirectoryIfMissing(dirname($requestFilePath));

        // Generate the request content with stub replacements
        $contents = $this->getReplacedContent($relatedModelName);

        // Check if the request file already exists
        if (! $this->files->exists($requestFilePath)) {
            // Create the request file
            $this->files->put($requestFilePath, $contents);
            $logMessage = "Request file has been created successfully at: {$requestFilePath}";
            $logStatus = CodeGeneratorFileLogStatus::SUCCESS;
            $this->info($logMessage);
        } else {
            // Log a warning if the request file already exists
            $logMessage = "Request file already exists at: {$requestFilePath}";
            $logStatus = CodeGeneratorFileLogStatus::ERROR;
            $this->warn($logMessage);
        }

        // Log the request creation details
        CodeGeneratorFileLog::create([
            'file_type' => CodeGeneratorFileType::REQUEST,
            'file_path' => $requestFilePath,
            'status'    => $logStatus,
            'message'   => $logMessage,
        ]);
    }


    /**
     * Get the path to the request stub file.
     *
     * @return string
     */
    protected function getStubPath(): string
    {
        // Return the path to the request stub file
        return __DIR__ . '/../../stubs/request.stub';
    }

    /**
     * Generate validation rules fields from command options.
     *
     * @return string
     */
    protected function getValidationFields(): string
    {
        $rules = $this->option('rules');

        if (!$rules) {
            return '';
        }

        $fields = explode(',', $rules);
        $lines = [];

        foreach ($fields as $field) {
            [$name, $rule] = explode(':', $field);
            $lines[] = self::INDENT . self::INDENT . "'" . $name . "' => '" . $rule . "',";
        }

        return implode("\n", $lines);
    }

    /**
     * Get the variables to replace in the stub file.
     *
     * @param string $relatedModelName
     * @return array
     */
    protected function getStubVariables($relatedModelName): array
    {
        // Get the related model name from the --model option
        $relatedModel = $this->option('model');

        // Return the variables to replace in the stub file
        return [
            'namespace'        => 'App\\' . config('code_generator.request_path', 'Http\Requests') . '\\' . $relatedModel,
            'class'            => 'Request',
            'validationFields' => $this->getValidationFields(),
        ];
    }

    /**
     * Replace stub variables with actual content.
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
     * Generate the final content for the request file.
     *
     * @param string $relatedModelName
     * @return string
     */
    protected function getReplacedContent($relatedModelName): string
    {
        // Generate the final content by replacing variables in the stub
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables($relatedModelName));
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
