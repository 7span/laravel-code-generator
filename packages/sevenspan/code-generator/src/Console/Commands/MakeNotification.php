<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Sevenspan\CodeGenerator\Models\CodeGeneratorFileLogs;

class MakeNotification extends Command
{
    const INDENT = '    ';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:custom-notification {className} {--modelName=} {--data=} {--body=} {--subject=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a custom notification with optional data, body, and subject.';
    public function __construct(protected Filesystem $files)
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $message = '';
        $status = "error";
        $name = Str::studly($this->argument('className'));
        $notificationFilePath = app_path('Notifications/' . $name . '.php');

        //make directory if doesn't exist
        $this->createDirectoryIfMissing(dirname($notificationFilePath));

        //with stub content replcament 
        $contents = $this->getReplacedContent($name);

        if (! $this->files->exists($notificationFilePath)) {
            $this->files->put($notificationFilePath, $contents);
            $message = "Notification File created: {$notificationFilePath}";
            $status = "success";
            $this->info($message);
        } else {
            $message = "Notification File already exists: {$notificationFilePath}";
            $status = "error";
            $this->warn($message);
        }
        CodeGeneratorFileLogs::create([
            'file_type' => 'Notification',
            'file_path' => $notificationFilePath,
            ' status' => $status,
            'message' => $message,
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
    protected function getReplacedContent($name): string
    {
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables($name));
    }
    protected function createDirectoryIfMissing($path): string
    {
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }
    protected function getStubPath(): string
    {
        return __DIR__ . '/../../stubs/notification.stub';
    }

    protected function getStubVariables($name): array
    {
        $dataOption = $this->option('data');
        $data = $this->parseDataOption($dataOption);
        $modelName = $this->option('modelName');

        return [
            'namespace' => 'App\Notifications',
            'class' => $name,
            'Model' => $modelName,
            'modelObject' => '$' . (Str::camel($modelName)),
            'subject' => $this->option('subject'),
            'body' => (string)$this->option('body'),
            'data' => $data,
        ];
    }

    protected function parseDataOption(?string $dataOption): string
    {
        if (!$dataOption) {
            return '';
        }

        $parsedData = [];
        foreach (explode(',', $dataOption) as $pair) {
            if (str_contains($pair, ':')) {
                [$key, $value] = explode(':', $pair);
                $parsedData[] = "'$key' => '$value'";
            }
        }

        return '[' . implode(', ', $parsedData) . ']';
    }
}

//notification support 
//model 
//class name=>action name
//notification type =>mail,db