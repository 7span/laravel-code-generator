<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Pluralizer;
use Illuminate\Filesystem\Filesystem;

class MakeQueryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:query {name} {--fields=} {--types=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make query file';

    /**
     * Filesystem instance
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * Create a new command instance.
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = $this->getSourceFilePath();
        $this->makeDirectory(dirname($path));

        $contents = $this->getSourceFile();

        if (! $this->files->exists($path)) {
            $this->files->put($path, $contents);
            $this->info("File : {$path} created");
        } else {
            $this->info("File : {$path} already exits");
        }
    }

    /**
     * Return the stub file path
     *
     * @return string
     */
    public function getStubPath()
    {
        return __DIR__ . '/../../../stubs/query.stub';
    }

    /**
     **
     * Map the stub variables present in stub to its value
     *
     * @return array
     */
    public function getStubVariables()
    {
        return [
            'NAMESPACE' => 'App\\GraphQL\\Query\\' . str_replace('ResourceQuery', '', $this->getSingularClassName($this->argument('name'))),
            'CLASSNAME' => $this->getSingularClassName($this->argument('name')),
            'SERVICE_CLASSNAME' => str_replace('ResourceQuery', '', $this->getSingularClassName($this->argument('name'))),
            'SERVICE_CLASSNAME_VARIABLE' => strtolower(str_replace('ResourceQuery', '', $this->getSingularClassName($this->argument('name')))),
        ];
    }

    /**
     * Get the stub path and the stub variables
     *
     * @return bool|mixed|string
     */
    public function getSourceFile()
    {
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables());
    }

    /**
     * Replace the stub variables(key) with the desire value
     *
     * @param  array  $stubVariables
     * @return bool|mixed|string
     */
    public function getStubContents($stub, $stubVariables = [])
    {
        $main_stub = __DIR__ . '/../../../stubs/query.stub';

        $upperContents = file_get_contents($main_stub);

        foreach ($stubVariables as $search => $replace) {
            $upperContents = str_replace('$' . $search . '$', $replace, $upperContents);
        }

        $fullContents = $upperContents;

        return $fullContents;
    }

    /**
     * Get the full path of generate class
     *
     * @return string
     */
    public function getSourceFilePath()
    {
        return base_path('app/GraphQL/Query') . '/' . str_replace('ResourceQuery', '', $this->getSingularClassName($this->argument('name'))) . '/' . $this->getSingularClassName($this->argument('name')) . '.php';
    }

    /**
     * Return the Singular Capitalize Name
     *
     * @return string
     */
    public function getSingularClassName($name)
    {
        return ucwords(Pluralizer::singular($name));
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }
}
