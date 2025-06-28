<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Sevenspan\CodeGenerator\Traits\FileManager;
use Sevenspan\CodeGenerator\Enums\CodeGeneratorFileType;

class MakeMigration extends Command
{
    use FileManager;

    private const INDENT = '    ';

    protected $signature = 'code-generator:migration {model : The name of the migration} 
                                                    {--fields=* : Array of field definitions with options like column_name, data_type, is_foreign_key, foreign_model_name, referenced_column, on_delete_action, on_update_action} 
                                                    {--softdelete : Include soft delete} 
                                                    {--overwrite : Overwrite the file if it exists}';

    protected $description = 'Create a custom migration file with optional fields, soft deletes, and deleted by functionality.';

    public function handle()
    {
        $tableName = Str::plural(Str::snake($this->argument('model')));
        $timestamp = now()->format('Y_m_d_His');

        // Define the migration file name and path
        $migrationFileName = "{$timestamp}_create_{$tableName}_table.php";
        $migrationFilePath = base_path(config('code-generator.paths.default.migration')) . "/{$migrationFileName}";

        File::ensureDirectoryExists(dirname($migrationFilePath));

        $contents = $this->getReplacedContent($tableName);

        // Create or overwrite file and get log the status and message
        $this->saveFile(
            $migrationFilePath,
            $contents,
            CodeGeneratorFileType::MIGRATION
        );
    }

    /**
     * Get the variables to replace in the stub file.
     *
     * @param string $tableName
     * @return array
     */
    protected function getStubVariables(string $tableName): array
    {
        $includeSoftDeletes = $this->option('softdelete');

        return [
            'tableName'        => $tableName,
            'fieldDefinitions' => $this->parseFieldsAndForeignKeys(),
            'softdelete'       => $includeSoftDeletes ? self::INDENT . "\$table->softDeletes();" : '',
            'deletedBy'        => $includeSoftDeletes ? "\$table->integer('deleted_by')->nullable();" : '',
        ];
    }

    /**
     * Parse fields and foreign keys from the fields option.
     * @return string
     */
    protected function parseFieldsAndForeignKeys(): string
    {
        $fieldsOption = $this->option('fields');


        if (empty($fieldsOption)) {
            return '';
        }

        $fieldLines = [];

        foreach ($fieldsOption as $field) {
            $name = $field['column_name'] ?? null;
            $type = $field['data_type'] ?? 'string';
            $isForeignKey = $field['is_foreign_key'] ?? false;
            if (!$name) {
                continue;
            }
            if ($name === 'deleted_at') {
                $fieldLines[] = "\$table->softDeletes();";
                continue;
            }
            if ($name === 'deleted_by') {
                $fieldLines[] = "\$table->integer('deleted_by')->nullable();";
                continue;
            }

            if ($isForeignKey && isset($field['foreign_model_name'])) {

                $relatedModel = $field['foreign_model_name'];
                $referenceKey = $field['referenced_column'] ?? 'id';
                $relatedTable = Str::snake(Str::plural($relatedModel));
                $foreignLine = "\$table->foreignId('{$name}')->references('{$referenceKey}')->on('{$relatedTable}')";

                // Add ON DELETE action if provided
                if (!empty($field['on_delete_action'])) {
                    $action = strtolower($field['on_delete_action']);
                    $foreignLine .= "->onDelete('{$action}')";
                }

                // Add ON UPDATE action if provided
                if (!empty($field['on_update_action'])) {
                    $action = strtolower($field['on_update_action']);
                    $foreignLine .= "->onUpdate('{$action}')";
                }

                $foreignLine .= ';';
                $fieldLines[] = $foreignLine;
            } else {
                $fieldLines[] = "\$table->{$type}('{$name}');";
            }
        }
        return implode(PHP_EOL . SELF::INDENT . SELF::INDENT . SELF::INDENT, $fieldLines);
    }

    /**
     * Generate the final content for the migration file.
     *
     * @param string $tableName
     * @return string
     */
    protected function getReplacedContent(string $tableName): string
    {
        return $this->getStubContents($this->getStubVariables($tableName));
    }

    /**
     * Replace the variables in the stub content with actual values.
     *
     * @param array $stubVariables
     * @return string
     */
    protected function getStubContents(array $stubVariables): string
    {
        $content = file_get_contents(__DIR__ . '/../../stubs/migration.stub');
        foreach ($stubVariables as $search => $replace) {
            $content = str_replace('{{ ' . $search . ' }}', $replace, $content);
        }
        return $content;
    }
}
