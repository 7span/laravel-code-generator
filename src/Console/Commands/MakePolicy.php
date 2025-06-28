<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Sevenspan\CodeGenerator\Library\Helper;
use Sevenspan\CodeGenerator\Traits\FileManager;
use Sevenspan\CodeGenerator\Enums\CodeGeneratorFileType;

class MakePolicy extends Command
{
    use FileManager;

    protected $signature = 'code-generator:policy {model : The related model for the policy.}
                                                 {--overwrite : is overwriting this file is selected}';

    protected $description = 'Generate a policy class for a specified model.';

    public function handle()
    {
        $policyClass = Str::studly($this->argument('model')) . "Policy";
        // Define the path for the policy file
        $policyFilePath = base_path(config('code-generator.paths.default.policy') . "/{$policyClass}.php");
        File::ensureDirectoryExists(dirname($policyFilePath));

        $content = $this->getReplacedContent($policyClass);

        // Create or overwrite file and get log the status and message
        $this->saveFile(
            $policyFilePath,
            $content,
            CodeGeneratorFileType::POLICY
        );
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
            'namespace'             => Helper::convertPathToNamespace(config('code-generator.paths.default.policy')),
            'class'                 => $policyClass,
            'model'                 => Str::studly($relatedModel),
            'relatedModelNamespace' => "use " . Helper::convertPathToNamespace(config('code-generator.paths.default.model')) . "\\" . Str::studly($relatedModel),
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
    protected function getStubContents(array $stubVariables): string
    {
        $content = file_get_contents(__DIR__ . '/../../stubs/policy.stub');

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
        return $this->getStubContents($this->getStubVariables($policyClass));
    }
}
