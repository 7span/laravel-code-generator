<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Sevenspan\CodeGenerator\Library\Helper;
use Sevenspan\CodeGenerator\Traits\FileManager;
use Sevenspan\CodeGenerator\Enums\CodeGeneratorFileType;

class MakeNotification extends Command
{
    use FileManager;

    const INDENT = '    ';

    protected $signature = 'code-generator:notification {className : Name of the notification class} 
                                                       {--model= : Related model name} 
                                                       {--data= : A comma-separated list of key-value pairs for notification data (e.g., key1:value1,key2:value2)} 
                                                       {--view= : Path where blade file will be generated for notificaction (e.g , emails\Delivery)}
                                                       {--subject= : The subject of the notification}
                                                       {--overwrite : is overwriting this file is selected}';

    protected $description = 'Generate a custom notification with optional data, bladefile and subject.';

    public function handle()
    {
        $notificationClass = Str::studly($this->argument('className')) . 'Notification';

        // Define the path for the notification file
        $notificationFilePath = base_path(config('code-generator.paths.default.notification') . "/{$notificationClass}.php");

        File::ensureDirectoryExists(dirname($notificationFilePath));
        $content = $this->getReplacedContent($notificationClass);

        $this->generateBladeFile($this->option('view'));

        $this->saveFile(
            $notificationFilePath,
            $content,
            CodeGeneratorFileType::NOTIFICATION
        );
    }

    /**
     * Get the contents of the stub file with replaced variables.
     *
     * @param array $stubVariables
     * @return string
     */
    protected function getStubContents(array $stubVariables): string
    {
        $content = file_get_contents(__DIR__ . '/../../stubs/notification.stub');

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
        return $this->getStubContents($this->getStubVariables($notificationClass));
    }

    /**
     * Get the variables to replace in the stub file.
     *
     * @param string $notificationClass
     * @return array
     */
    protected function getStubVariables($notificationClass): array
    {
        $parsedData = $this->parseDataOption($this->option('data'));
        $relatedModel = $this->option('model');

        return [
            'namespace'              => Helper::convertPathToNamespace(config('code-generator.paths.default.notification')),
            'class'                  => $notificationClass,
            'model'                  => $relatedModel,
            'relatedModelNamespace'  => "use " . Helper::convertPathToNamespace(config('code-generator.paths.default.model')) . '\\' . $relatedModel,
            'modelObject'            => '$' . (Str::camel($relatedModel)),
            'subject'                => $this->option('subject'),
            'bladePath'                   => $this->option('view') ? str_replace(['/'], '.', $this->option('view')) : null,
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
        if (!$dataOption) {
            return '';
        }

        $parsedData = [];

        foreach (explode(',', $dataOption) as $key) {
            $key = trim($key);
            if ($key !== '') {
                $parsedData[] = "'$key' => null";
            }
        }

        if (!empty($parsedData)) {
            return  implode(',' . PHP_EOL . self::INDENT . self::INDENT . self::INDENT . self::INDENT, $parsedData);
        }
        return '';
    }

    protected function generateBladeFile(string $bladePath)
    {
        if (!$bladePath) {
            return;
        }

        $bladeFilePath = resource_path('views/' . str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $bladePath) . '.blade.php');

        File::ensureDirectoryExists(dirname($bladeFilePath));
        $contents = "<!-- Notification Blade View for {$bladePath} -->";
        $this->saveFile($bladeFilePath, $contents, CodeGeneratorFileType::BLADE);
    }
}
