<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Sevenspan\CodeGenerator\Models\CodeGeneratorFileLogs;

class MakeMigration extends Command
{
    protected $signature = 'make:custom-migration 
                            {name} 
                            {--fields=}
                            {--softdelete} 
                            {--deletedBy}';

    protected $description = 'Generate a migration file using a stub template.';

    public function __construct(protected Filesystem $files)
    {
        parent::__construct();
    }

    public function handle()
    {
        $message = '';
        $status = "error";
        $tableName = Str::snake($this->argument('name'));
        $timestamp = now()->format('Y_m_d_His');
        $fileName = "{$timestamp}_create_{$tableName}_table.php";
        $migrationPath = base_path("database/migrations/{$fileName}");

        $this->createDirectoryIfMissing(dirname($migrationPath));

        $contents = $this->getReplacedContent($tableName);

        if (! $this->files->exists($migrationPath)) {
            $this->files->put($migrationPath, $contents);
            $message = "Migration created: {$migrationPath}";
            $status = "success";
            $this->info($message);
        } else {
            $message = "Migration already exists: {$migrationPath}";
            $status = "error";
            $this->warn($message);
        }

        CodeGeneratorFileLogs::create([
            'file_type' => 'migration',
            'file_path' => $migrationPath,
            'status' => $status,
            'message' => $message,
            'created_at' => now(),
        ]);
    }

    protected function getStubPath(): string
    {
        return __DIR__ . '/../../stubs/migration.create.stub';
    }

    protected function getStubVariables(string $table): array
    {
        $softdelete = $this->option('softdelete');
        $deletedBy = $this->option('deletedBy');
        $fieldsOption = $this->option('fields');

        $fieldsLine = $this->parseFields($fieldsOption);
        return [
            'table' => $table,
            'fieldsLine' => $fieldsLine,
            'softdeleteLine' => $softdelete ? "  \$table->softDeletes();" : '',
            'deletedByLine' => $deletedBy ? "   \$table->integer('deleted_by')->nullable();" : '',
        ];
    }
    protected function parseFields(?string $fieldsOption): string
    {
        if (!$fieldsOption) {
            return '';
        }

        $lines = [];
        $fields = explode(',', $fieldsOption);

        foreach ($fields as $field) {
            // Expected format: name:type, e.g., provider:text
            [$name, $type] = array_map('trim', explode(':', $field));

            // Generate line like: $table->text('provider');
            $lines[] = "\$table->{$type}('{$name}');";
        }

        return implode("\n", $lines);
    }


    protected function getReplacedContent(string $table): string
    {
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables($table));
    }

    protected function getStubContents(string $stubPath, array $stubVariables): string
    {
        $content = file_get_contents($stubPath);

        foreach ($stubVariables as $search => $replace) {
            $content = str_replace('{{ ' . $search . ' }}', $replace, $content);
        }

        return $content;
    }

    protected function createDirectoryIfMissing($path): string
    {
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }
}
//make:migration Test --fields=name:string,age:integer,gender:string --softdelete --deletedBy=1
