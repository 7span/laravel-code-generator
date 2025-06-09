<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
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

    public function __construct(protected Filesystem $files)
    {
        parent::__construct();
    }

    public function handle()
    {
        $tableName = Str::plural(Str::snake($this->argument('model')));
        $timestamp = now()->format('Y_m_d_His');

        // Define the migration file name and path
        $migrationFileName = "{$timestamp}_create_{$tableName}_table.php";
        $migrationFilePath = base_path("database/" . config('code-generator.paths.migration', 'Migration') . "/{$migrationFileName}");

        $this->createDirectoryIfMissing(dirname($migrationFilePath));

        $contents = $this->getReplacedContent($tableName);

        // Create or overwrite file and get log the status and message
        $this->saveFile(
            $migrationFilePath,
            $contents,
            CodeGeneratorFileType::MIGRATION
        );
    }

    /**
     * @return string
     */
    protected function getStubPath(): string
    {
        return __DIR__ . '/../../stubs/migration.stub';
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

            if ($isForeignKey && isset($field['foreign_model_name'])) {

                $relatedModel = $field['foreign_model_name'];
                $referenceKey = $field['referenced_column'] ?? 'id';
                $relatedTable = Str::snake(Str::plural($relatedModel));
                $foreignLine = self::INDENT . self::INDENT . self::INDENT . "\$table->foreignId('{$name}')->references('{$referenceKey}')->on('{$relatedTable}')";

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
                $fieldLines[] = self::INDENT . self::INDENT . self::INDENT . "\$table->{$type}('{$name}');";
            }
        }
        return implode(PHP_EOL, $fieldLines);
    }

    /**
     * Generate the final content for the migration file.
     *
     * @param string $tableName
     * @return string
     */
    protected function getReplacedContent(string $tableName): string
    {
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables($tableName));
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
     * @param string $path
     */
    protected function createDirectoryIfMissing($path)
    {
        if (!$this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }
    }
}
