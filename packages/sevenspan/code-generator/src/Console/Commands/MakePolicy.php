<?php


namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Sevenspan\CodeGenerator\Models\CodeGeneratorFileLogs;

class MakePolicy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:policy {name} {--model=} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a policy class for a specified model.';
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
        $policyName = Str::studly($this->argument('name'));
        $policyPath = app_path('Policies/' . $policyName . 'Policy.php');

        $this->createDirectoryIfMissing(dirname($policyPath));

        //with stub content replcament 
        $contents = $this->getReplacedContent($policyName);

        if (! $this->files->exists($policyPath)) {
            $this->files->put($policyPath, $contents);
            $message = "Policy created: {$policyPath}";
            $status = "success";
            $this->info($message);
        } else {
            $message = "Policy already exists: {$policyPath}";
            $this->warn($message);
        }

        CodeGeneratorFileLogs::create([
            'file_type' => 'Policy',
            'file_path' => $policyPath,
            'status' => $status,
            'message' => $message,
            'created_at' => now(),
        ]);
    }
    protected function getStubPath(): string
    {
        return __DIR__ . '/../../stubs/policy.stub';
    }
    protected function getStubVariables($policyName): array
    {
        $modelName = $this->option('model');
        return [
            'namespace' => 'App\\Policies',
            'class'     => $policyName,
            'model' => Str::studly($modelName),
            'modelVariable' => Str::camel($modelName),
        ];
    }

    protected function getStubContents(string $stubPath, array $stubVariables): string
    {
        $content = file_get_contents($stubPath);

        foreach ($stubVariables as $search => $replace) {
            $content = str_replace('{{ ' . $search . ' }}', $replace, $content);
        }

        return $content;
    }

    protected function getReplacedContent($policyName): string
    {
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables($policyName));
    }
    protected function createDirectoryIfMissing($path): string
    {
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }
}
//make:policy Test --model=Test