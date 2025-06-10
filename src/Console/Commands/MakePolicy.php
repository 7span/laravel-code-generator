<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Sevenspan\CodeGenerator\Traits\FileManager;
use Sevenspan\CodeGenerator\Enums\CodeGeneratorFileType;

class MakePolicy extends Command
{
    use FileManager;

    protected $signature = 'code-generator:policy {model : The related model for the policy.}
                                                 {--overwrite : is overwriting this file is selected}';

    protected $description = 'Generate a policy class for a specified model.';

    public function __construct(protected Filesystem $files)
    {
        parent::__construct();
    }

    public function handle()
    {
        $policyClass = Str::studly($this->argument('model')) . "Policy";
        // Define the path for the policy file
        $policyFilePath = base_path(config('code-generator.paths.policy', 'App\Policies') . "/{$policyClass}.php");
        $this->createDirectoryIfMissing(dirname($policyFilePath));

        $content = $this->getReplacedContent($policyClass);

        // Create or overwrite file and get log the status and message
        $this->saveFile(
            $policyFilePath,
            $content,
            CodeGeneratorFileType::POLICY
        );
    }

    /**
     * @return string
     */
    protected function getStubPath(): string
    {
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
        $relatedModel = $this->argument('model');

        return [
            'namespace'             => config('code-generator.paths.policy', 'App\Policies'),
            'class'                 => $policyClass,
            'model'                 => Str::studly($relatedModel),
            'relatedModelNamespace' => "use " . config('code-generator.paths.model', 'App\Models') . "\\" . Str::studly($relatedModel),
            'modelInstance'         => Str::camel($relatedModel),
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
        $content = file_get_contents($stubPath);

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
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables($policyClass));
    }

    /**
     * @param string $path
     */
    protected function createDirectoryIfMissing($path): void
    {
        // Create the directory if it doesn't exist
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }
    }
}
