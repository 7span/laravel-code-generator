<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Sevenspan\CodeGenerator\Library\Helper;
use Sevenspan\CodeGenerator\Traits\FileManager;
use Sevenspan\CodeGenerator\Enums\CodeGeneratorFileType;

class MakeRequest extends Command
{
    use FileManager;

    private const INDENT = '    ';

    protected $signature = 'code-generator:request  {model : The related model for the observer.}
                                                   {--rules= :comma seperated list of rules (e.g, Name:required,email:nullable )} 
                                                   {--overwrite : is overwriting this file is selected}';

    protected $description = 'Generate a custom form request with validation rules';

    public function handle()
    {
        $relatedModelName = Str::studly($this->argument('model'));

        // Define the path for the request file
        $requestFilePath = base_path(config('code-generator.paths.default.request') . "/{$relatedModelName}" . "/Request.php");
        File::ensureDirectoryExists(dirname($requestFilePath));

        $content = $this->getReplacedContent($relatedModelName);

        // Create or overwrite file and get log the status and message
        $this->saveFile(
            $requestFilePath,
            $content,
            CodeGeneratorFileType::REQUEST
        );
    }

    /**
     * Generate validation rules fields from command options.
     *
     * @return string
     */
    protected function getValidationFields(): string
    {
        $rules = $this->option('rules');

        if (!$rules) return '';

        $fields = explode(',', $rules);
        $lines = [];

        foreach ($fields as $field) {
            [$name, $rule] = explode(':', $field);
            if (in_array($name, ['deleted_at', 'deleted_by'])) {
                continue;
            }
            $lines[] = "'" . $name . "' => '" . $rule . "',";
        }

        return implode(PHP_EOL . SELF::INDENT . SELF::INDENT . SELF::INDENT, $lines);
    }

    /**
     * Get the variables to replace in the stub file.
     *
     * @param string $relatedModelName
     * @return array
     */
    protected function getStubVariables($relatedModelName): array
    {
        $relatedModelName = $this->argument('model');
        return [
            'namespace'        => Helper::convertPathToNamespace(config('code-generator.paths.default.request')) . '\\' . $relatedModelName,
            'class'            => 'Request',
            'validationFields' => $this->getValidationFields(),
        ];
    }

    /**
     * Replace stub variables with actual content.
     * 
     * @param array $stubVariables
     * @return string
     */
    protected function getStubContents(array $stubVariables): string
    {
        $content = file_get_contents(__DIR__ . '/../../stubs/request.stub');
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
        return $this->getStubContents($this->getStubVariables($relatedModelName));
    }
}
