<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Sevenspan\CodeGenerator\Enums\CodeGeneratorFileType;
use Sevenspan\CodeGenerator\Models\CodeGeneratorFileLog;
use Sevenspan\CodeGenerator\Enums\CodeGeneratorFileLogStatus;

class MakeMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'codegenerator:migration 
                            {name : The name of the migration} 
                            {--fields= : A of fields with their types (e.g., name:string,age:integer)} 
                            {--softdelete : Include soft delete} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a custom migration file with optional fields, soft deletes, and deleted by functionality.';

    /**
     * Constructor to initialize the Filesystem dependency.
     *
     * @param Filesystem $files
     */
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
        $logMessage = '';
        $tableName = Str::snake($this->argument('name'));

        // Generate a timestamp for the migration file name
        $timestamp = now()->format('Y_m_d_His');

        // Define the migration file name and path
        $migrationFileName = "{$timestamp}_create_{$tableName}_table.php";
        $migrationFilePath = base_path("database/" . config('code_generator.migration_path', 'Migration') . "/{$migrationFileName}");

        // Ensure the directory exists
        $this->createDirectoryIfMissing(dirname($migrationFilePath));

        // Generate the migration content with stub replacements
        $contents = $this->getReplacedContent($tableName);

        // Check if the migration file already exists
        if (! $this->files->exists($migrationFilePath)) {
            // Create the migration file
            $this->files->put($migrationFilePath, $contents);
            $logMessage = "Migration file has been created successfully at: {$migrationFilePath}";
            $logStatus = CodeGeneratorFileLogStatus::SUCCESS;
            $this->info($logMessage);
        } else {
            // Log a warning if the migration file already exists
            $logMessage = "Migration file already exists at: {$migrationFilePath}";
            $logStatus = CodeGeneratorFileLogStatus::ERROR;
            $this->warn($logMessage);
        }

        // Log the migration creation details
        CodeGeneratorFileLog::create([
            'file_type' => CodeGeneratorFileType::MIGRATION,
            'file_path' => $migrationFilePath,
            'status'    => $logStatus,
            'message'   => $logMessage,
        ]);
    }

    /**
     * Get the path to the migration stub file.
     *
     * @return string
     */
    protected function getStubPath(): string
    {
        // Return the path to the migration stub file
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
        // Check if soft deletes and deleted_by column included
        $includeSoftDeletes = $this->option('softdelete');

        $fieldDefinitions = $this->parseFields($this->option('fields'));

        // Return the variables to replace in the stub file
        return [
            'tableName'        => $tableName,
            'fieldDefinitions' => $fieldDefinitions,
            'softdelete'       => $includeSoftDeletes ? "  \$table->softDeletes();" : '',
        ];
    }

    /**
     * Parse the --fields option into migration field definitions.
     *
     * @param string|null $fieldsOption
     * @return string
     */
    protected function parseFields(?string $fieldsOption): string
    {
        // Return an empty string if no fields are provided
        if (!$fieldsOption) {
            return '';
        }

        $fieldLines = [];
        $fields = explode(',', $fieldsOption);

        // Parse each field and its type
        foreach ($fields as $field) {
            // Expected format: name:type, e.g., provider:text
            [$name, $type] = array_map('trim', explode(':', $field));

            // Generate a line like: $table->text('provider');
            $fieldLines[] = "\$table->{$type}('{$name}');";
        }

        // Combine all field definitions into a single string
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
        // Generate the final content by replacing variables in the stub
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
        // Read the stub file content
        $content = file_get_contents($stubPath);

        // Replace each variable in the stub content
        foreach ($stubVariables as $search => $replace) {
            $content = str_replace('{{ ' . $search . ' }}', $replace, $content);
        }

        return $content;
    }

    /**
     * Create a directory if it does not already exist.
     *
     * @param string $path
     * @return string
     */
    protected function createDirectoryIfMissing($path): string
    {
        // Create the directory if it doesn't exist
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }
}
