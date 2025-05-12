<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Sevenspan\CodeGenerator\Models\CodeGeneratorFileLogs;

class MakeFactory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:factory {model} {--fields=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a factory file for a given model with optional fields';
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
        $model = Str::studly($this->argument('model'));
        $factoryPath = base_path("database/factories/{$model}Factory.php");
        $this->createDirectoryIfMissing(dirname($factoryPath));
        $fields = $this->parseFieldsOption($this->option('field'));

        //with stub content replcament 
        $contents = $this->getReplacedContent($model, $fields);

        if (! $this->files->exists($factoryPath)) {
            $this->files->put($factoryPath, $contents);
            $message = "Factory created: {$factoryPath}";
            $status = "success";
            $this->info($message);
        } else {
            $message = "Factory already exists: {$factoryPath}";
            $status = "error";
            $this->warn($message);
        }



        CodeGeneratorFileLogs::create([
            'file_type' => 'Factory',
            'file_path' => $factoryPath,
            ' status' => $status,
            'message' => $message,
            'created_at' => now(),
        ]);
    }
    protected function getStubPath(): string
    {
        return __DIR__ . '/../../stubs/factory.stub';
    }

    protected function parseFieldsOption(?string $fieldsOption): array
    {
        $parsed = [];

        if (!$fieldsOption) {
            return $parsed;
        }

        foreach (explode(',', $fieldsOption) as $pair) {
            if (str_contains($pair, ':')) {
                [$name, $type] = explode(':', $pair);
                $parsed[trim($name)] = trim($type);
            }
        }

        return $parsed;
    }

    protected function getFactoryField(string $column, string $type): string
    {
        $fakerMapping = [
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

        return $fakerMapping[$type] ?? "'{$column}' => null";
    }
    protected function generateFactoryFields(array $fields): string
    {
        $fieldLines = [];

        foreach ($fields as $column => $type) {
            $fieldLines[] = '      ' . $this->getFactoryField($column, $type) . ',';
        }

        return implode("\n", $fieldLines);
    }


    protected function getStubVariables($model, $fields): array
    {
        return [
            'factoryNamespace' => 'Database\Factories',
            'namespacedModel' => 'App\Models\\' . $model,
            'factory'     => $model,
            'fields' => $this->generateFactoryFields($fields),

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

    protected function getReplacedContent(string $model, $field): string
    {
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables($model, $field));
    }
    protected function createDirectoryIfMissing($path): string
    {
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }
}

//make:factory Test --fields='name:string,age:integer'
