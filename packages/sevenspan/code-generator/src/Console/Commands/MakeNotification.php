<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Sevenspan\CodeGenerator\Enums\CodeGeneratorFileType;
use Sevenspan\CodeGenerator\Models\CodeGeneratorFileLog;
use Sevenspan\CodeGenerator\Enums\CodeGeneratorFileLogStatus;

class MakeNotification extends Command
{
    const INDENT = '    ';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'codegenerator:notification {className : Name of the notification class} 
                                                           {--modelName= : Related model name} 
                                                           {--data= : A comma-separated list of key-value pairs for notification data (e.g., key1:value1,key2:value2)} 
                                                           {--body= : The body content of the notification} 
                                                           {--subject= : The subject of the notification}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a custom notification with optional data, body, and subject.';

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

        // Get the notification class name from the command argument
        $notificationClass = Str::studly($this->argument('className'));

        // Define the path for the notification file
        $notificationFilePath = app_path(config('code_generator.notification_path', 'Notification') . "/{$notificationClass}.php");

        // Create the directory if it doesn't exist
        $this->createDirectoryIfMissing(dirname($notificationFilePath));

        // Generate the notification content with stub replacements
        $contents = $this->getReplacedContent($notificationClass);

        // Check if the notification file already exists
        if (! $this->files->exists($notificationFilePath)) {
            // Create the notification file
            $this->files->put($notificationFilePath, $contents);
            $logMessage = "Notification file has been created successfully at: {$notificationFilePath}";
            $logStatus = CodeGeneratorFileLogStatus::SUCCESS;
            $this->info($logMessage);
        } else {
            // Log a warning if the notification file already exists
            $logMessage = "Notification file already exists at: {$notificationFilePath}";
            $logStatus = CodeGeneratorFileLogStatus::ERROR;
            $this->warn($logMessage);
        }

        // Log the notification creation details
        CodeGeneratorFileLog::create([
            'file_type' => CodeGeneratorFileType::NOTIFICATION,
            'file_path' => $notificationFilePath,
            'status'    => $logStatus,
            'message'   => $logMessage,
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
     * Generate the final content for the notification file.
     *
     * @param string $notificationClass
     * @return string
     */
    protected function getReplacedContent($notificationClass): string
    {
        // Generate the final content by replacing variables in the stub
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables($notificationClass));
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

    /**
     * Get the path to the notification stub file.
     *
     * @return string
     */
    protected function getStubPath(): string
    {
        // Return the path to the notification stub file
        return __DIR__ . '/../../stubs/notification.stub';
    }

    /**
     * Get the variables to replace in the stub file.
     *
     * @param string $notificationClass
     * @return array
     */
    protected function getStubVariables($notificationClass): array
    {
        // Parse the --data option into an array
        $dataOption = $this->option('data');
        $parsedData = $this->parseDataOption($dataOption);

        // Get the related model name from the --modelName option
        $relatedModel = $this->option('modelName');

        // Return the variables to replace in the stub file
        return [
            'namespace'              => 'App\\' . config('code_generator.notification_path', 'Notification'),
            'class'                  => $notificationClass,
            'model'                  => $relatedModel,
            'relatedModelNamespace' => config('code_generator.model_path', 'Models') . '\\' . $relatedModel,
            'modelObject'            => '$' . (Str::camel($relatedModel)),
            'subject'                => $this->option('subject'),
            'body'                   => (string) $this->option('body'),
            'data'                   => $parsedData,
        ];
    }

    /**
     * Parse the --data option into an associative array.
     *
     * @param string|null $dataOption
     * @return string
     */
    protected function parseDataOption(?string $dataOption): string
    {
        // Return an empty string if no data is provided
        if (! $dataOption) {
            return '';
        }

        $parsedData = [];

        // Parse each key-value pair from the --data option
        foreach (explode(',', $dataOption) as $pair) {
            if (str_contains($pair, ':')) {
                [$key, $value] = explode(':', $pair);
                $parsedData[] = "'$key' => '$value'";
            }
        }

        // Combine all key-value pairs into a single string
        return '[' . implode(', ', $parsedData) . ']';
    }
}
