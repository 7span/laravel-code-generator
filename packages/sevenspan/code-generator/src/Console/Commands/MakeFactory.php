<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Sevenspan\CodeGenerator\Enums\FileGenerationStatus;
use Sevenspan\CodeGenerator\Models\CodeGeneratorFileLog;

class MakeFactory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'codegenerator:factory 
                                          {model : The name of the model for which the factory file will be generated.} 
                                          {--fields= : A comma-separated list of fields with their types (e.g., name:string,id:integer).}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a factory file for a given model with optional fields';

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

        // Get the model name from the command argument
        $model = Str::studly($this->argument('model'));

        // Define the path for the factory file
        $factoryFilePath = base_path("database/factories/{$model}Factory.php");

        // Ensure the directory exists
        $this->createDirectoryIfMissing(dirname($factoryFilePath));

        // Parse fields from the --fields option
        $fields = $this->parseFieldsOption($this->option('fields'));

        // Generate the factory content with stub replacements
        $contents = $this->getReplacedContent($model, $fields);

        // Check if the factory file already exists
        if (! $this->files->exists($factoryFilePath)) {
            // Create the factory file
            $this->files->put($factoryFilePath, $contents);
            $logMessage = "Factory file has been created successfully at: {$factoryFilePath}";
            $logStatus = FileGenerationStatus::SUCCESS;
            $this->info($logMessage);
        } else {
            // Log a warning if the factory file already exists
            $logMessage = "Factory file already exists at: {$factoryFilePath}";
            $logStatus = FileGenerationStatus::ERROR;
            $this->warn($logMessage);
        }

        // Log the factory creation details
        CodeGeneratorFileLog::create([
            'file_type' => 'Factory',
            'file_path' => $factoryFilePath,
            'status' => $logStatus,
            'message' => $logMessage,
        ]);
    }

    /**
     * Get the path to the factory stub file.
     *
     * @return string
     */
    protected function getStubPath(): string
    {
        // Return the path to the factory stub file
        return __DIR__ . '/../../stubs/factory.stub';
    }

    /**
     * Parse the --fields option into an associative array.
     *
     * @param string|null $fieldsOption
     * @return array
     */
    protected function parseFieldsOption(?string $fieldsOption): array
    {
        $parsedFields = [];

        // Return an empty array if no fields are provided
        if (!$fieldsOption) {
            return $parsedFields;
        }

        // Parse each field and its type from the --fields option
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
     * @param string $column
     * @param string $type
     * @return string
     */
    protected function getFactoryField(string $column, string $type): string
    {
        // Map field types to Faker methods
        $fakerTypeMapping = [
            'string' => "'{$column}' => \$this->faker->word",
            'text' => "'{$column}' => \$this->faker->text",
            'integer' => "'{$column}' => \$this->faker->numberBetween(1, 100)",
            'bigint' => "'{$column}' => \$this->faker->randomNumber()",
            'boolean' => "'{$column}' => \$this->faker->boolean",
            'datetime' => "'{$column}' => \$this->faker->dateTime()",
            'date' => "'{$column}' => \$this->faker->date()",
            'time' => "'{$column}' => \$this->faker->time()",
            'email' => "'{$column}' => \$this->faker->unique()->safeEmail",
            'name' => "'{$column}' => \$this->faker->name",
            'uuid' => "'{$column}' => \$this->faker->uuid",
        ];

        // Return the corresponding Faker method or null if the type is not mapped
        return $fakerTypeMapping[$type] ?? "'{$column}' => null";
    }

    /**
     * Generate the factory fields as a string for the stub.
     *
     * @param array $fields
     * @return string
     */
    protected function generateFactoryFields(array $fields): string
    {
        $factoryFieldLines = [];

        // Generate each field definition
        foreach ($fields as $column => $type) {
            $factoryFieldLines[] = '      ' . $this->getFactoryField($column, $type) . ',';
        }

        // Combine all field definitions into a single string
        return implode("\n", $factoryFieldLines);
    }

    /**
     * Get the variables to replace in the factory stub.
     *
     * @param string $model
     * @param array $fields
     * @return array
     */
    protected function getStubVariables(string $model, array $fields): array
    {
        // Return the variables to replace in the stub file
        return [
            'factoryNamespace' => 'Database\Factories',
            'namespacedModel' => 'App\Models\\' . $model,
            'factory' => $model,
            'fields' => $this->generateFactoryFields($fields),
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
        // Read the stub file content
        $content = file_get_contents($stubPath);

        // Replace each variable in the stub content
        foreach ($stubVariables as $search => $replace) {
            $content = str_replace('{{ ' . $search . ' }}', $replace, $content);
        }

        return $content;
    }

    /**
     * Generate the final content for the factory file.
     *
     * @param string $model
     * @param array $fields
     * @return string
     */
    protected function getReplacedContent(string $model, array $fields): string
    {
        // Generate the final content by replacing variables in the stub
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables($model, $fields));
    }

    /**
     * Create a directory if it does not already exist.
     *
     * @param string $path
     * @return string
     */
    protected function createDirectoryIfMissing(string $path): string
    {
        // Create the directory if it doesn't exist
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }
}
