<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Sevenspan\CodeGenerator\Enums\CodeGeneratorFileType;
use Sevenspan\CodeGenerator\Models\CodeGeneratorFileLog;
use Sevenspan\CodeGenerator\Traits\ManagesFileCreationAndOverwrite;

class MakeMigration extends Command
{
    use ManagesFileCreationAndOverwrite;
    protected $signature = 'codegenerator:migration {modelName : The name of the migration} 
                                                    {--fields= : A of fields with their types (e.g., name:string,age:integer)} 
                                                    {--softdelete : Include soft delete} 
                                                    {--overwrite : is overwriting this file is selected}';

    protected $description = 'Create a custom migration file with optional fields, soft deletes, and deleted by functionality.';


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
        $tableName = Str::snake($this->argument('modelName'));

        $timestamp = now()->format('Y_m_d_His');

        // Define the migration file name and path
        $migrationFileName = "{$timestamp}_create_{$tableName}_table.php";
        $migrationFilePath = base_path("database/" . config('code_generator.migration_path', 'Migration') . "/{$migrationFileName}");

        $this->createDirectoryIfMissing(dirname($migrationFilePath));

        $content = $this->getReplacedContent($tableName);

        // Create or overwrite migration file and get the status and message
        [$logStatus, $logMessage, $isOverwrite] = $this->createOrOverwriteFile(
            $migrationFilePath,
            $content,
            'Migration'
        );

        CodeGeneratorFileLog::create([
            'file_type' => CodeGeneratorFileType::MIGRATION,
            'file_path' => $migrationFilePath,
            'status'    => $logStatus,
            'message'   => $logMessage,
            'is_overwrite' => $isOverwrite,
        ]);
    }

    /**
     * @return string
     */
    protected function getStubPath(): string
    {
        return __DIR__ . '/../../stubs/migration.create.stub';
    }

    /**
     * Get the variables to replace in the stub file.
     *
     * @param string $tableName
     * @return array
     */
    protected function getStubVariables(string $tableName): array
    {
        $fieldsOption = $this->option('fields');

        $fieldDefinitions = $this->parseFields($fieldsOption, $hasDeletedBy);

        $includeSoftDeletes = $this->option('softdelete');

        return [
            'tableName'        => $tableName,
            'fieldDefinitions' => $fieldDefinitions,
            'softdelete'       => $includeSoftDeletes ? "  \$table->softDeletes();" : '',
            'deletedBy'        => $hasDeletedBy ? "\$table->integer('deleted_by')->nullable();" : '',
        ];
    }

    /**
     * Parse the --fields option into migration field definitions.
     *
     * @param string|null $fieldsOption
     * @param bool &$hasDeletedBy - Outputs true if 'deleted_by' field is present
     * @return string
     */
    protected function parseFields(?string $fieldsOption, &$hasDeletedBy = false): string
    {
        $hasDeletedBy = false;

        if (!$fieldsOption) {
            return '';
        }

        $fieldLines = [];
        $fields = explode(',', $fieldsOption);

        foreach ($fields as $field) {
            [$name, $type] = array_map('trim', explode(':', $field));

            // Detect if 'deleted_by' is in the fields
            if (Str::lower($name) === 'deleted_by') {
                $hasDeletedBy = true;
                continue;
            }

            $fieldLines[] = "            \$table->{$type}('{$name}');";
        }

        return implode("\n", $fieldLines);
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
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }
    }
}
