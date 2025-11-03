<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Sevenspan\CodeGenerator\Library\Helper;
use Sevenspan\CodeGenerator\Traits\FileManager;
use Sevenspan\CodeGenerator\Enums\CodeGeneratorFileType;

class MakeFactory extends Command
{
    use FileManager;

    private const INDENT = '    ';

    protected $signature = 'code-generator:factory {model : The name of the model for which the factory file will be generated.} 
                                                  {--fields= : A comma-separated list of fields with their types (e.g., name:string,id:integer).}
                                                  {--overwrite : is overwriting this file is selected}';

    protected $description = 'Generate a factory file for a given model with optional fields';

    public function handle()
    {
        $modelName = Str::studly($this->argument('model'));

        // Define the path for the factory file
        $factoryFilePath = base_path(config('code-generator.paths.default.factory') . "/{$modelName}Factory.php");

        File::ensureDirectoryExists(dirname($factoryFilePath));

        // Parse fields from the --fields option
        $fields = $this->parseFieldsOption($this->option('fields'));

        $content = $this->getReplacedContent($modelName, $fields);

        // Create or overwrite file and get log the status and message
        $this->saveFile(
            $factoryFilePath,
            $content,
            CodeGeneratorFileType::FACTORY
        );
    }

    /**
     * Parse the --fields option into an associative array.
     *
     * @param  string|null  $fieldsOption
     * @return array
     */
    protected function parseFieldsOption(?string $fieldsOption): array
    {
        $parsedFields = [];

        if (! $fieldsOption) {
            return $parsedFields;
        }

        foreach (explode(',', $fieldsOption) as $pair) {
            if (str_contains($pair, ':')) {
                [$name, $type] = explode(':', $pair);
                $parsedFields[trim($name)] = trim($type);
            }
        }

        return $parsedFields;
    }

    /**
     * Generate a factory field definition based on the column name and type.
     *
     * @param  string  $column
     * @param  string  $type
     * @return string
     */
    protected function getFactoryField(string $column, string $type): string
    {
        $type = match ($type) {
            'double', 'float', 'decimal' => 'float',
            default => $type,
        };
        $fakerTypeMapping = [
            'string'    => "'{$column}' => fake()->word",
            'text'      => "'{$column}' => fake()->text",
            'integer'   => "'{$column}' => fake()->numberBetween(1, 100)",
            'bigInteger'    => "'{$column}' => fake()->randomNumber()",
            'float'     => "'{$column}' => fake()->randomFloat(2, 0, 1000)",
            'boolean'   => "'{$column}' => fake()->boolean",
            'datetime'  => "'{$column}' => fake()->dateTime()",
            'date'      => "'{$column}' => fake()->date()",
            'timestamp' => "'{$column}' => fake()->time()",
            'varchar'   => "'{$column}' => fake()->word",
            'email'     => "'{$column}' => fake()->unique()->safeEmail",
            'name'      => "'{$column}' => fake()->name",
            'uuid'      => "'{$column}' => fake()->uuid",
        ];

        return $fakerTypeMapping[$type] ?? "'{$column}' => null";
    }

    /**
     * Generate the factory fields as a string for the stub.
     *
     * @param  array  $fields
     * @return string
     */
    protected function generateFactoryFields(array $fields): string
    {
        $factoryFieldLines = [];

        foreach ($fields as $column => $type) {
            if (in_array($column, ['deleted_at', 'deleted_by'])) {
                continue;
            }
            $factoryFieldLines[] = $this->getFactoryField($column, $type) . ',';
        }

        return implode(PHP_EOL . SELF::INDENT . SELF::INDENT . SELF::INDENT, $factoryFieldLines);
    }

    /**
     * Get the variables to replace in the factory stub.
     *
     * @param  string  $modelName
     * @param  array  $fields
     * @return array
     */
    protected function getStubVariables(string $modelName, array $fields): array
    {
        return [
            'factoryNamespace'       => Helper::convertPathToNamespace(config('code-generator.paths.default.factory')),
            'relatedModelNamespace'  => Helper::convertPathToNamespace(config('code-generator.paths.default.model')) . "\\" . $modelName,
            'factory'                => $modelName . "Factory",
            'fields'                 => $this->generateFactoryFields($fields),
        ];
    }

    /**
     * Replace the variables in the stub content with actual values.
     *
     * @param  array  $stubVariables
     * @return string
     */
    protected function getStubContents(array $stubVariables): string
    {
        $content = file_get_contents(__DIR__ . '/../../stubs/factory.stub');
        foreach ($stubVariables as $search => $replace) {
            $content = str_replace('{{ ' . $search . ' }}', $replace, $content);
        }

        return $content;
    }

    /**
     * Generate the final content for the factory file.
     *
     * @param  string  $modelName
     * @param  array  $fields
     * @return string
     */
    protected function getReplacedContent(string $modelName, array $fields): string
    {
        return $this->getStubContents($this->getStubVariables($modelName, $fields));
    }
}
