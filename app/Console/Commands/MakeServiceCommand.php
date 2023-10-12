<?php

namespace App\Console\Commands;

use Str;
use Illuminate\Console\Command;
use Illuminate\Support\Pluralizer;
use Illuminate\Filesystem\Filesystem;

class MakeServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name} {--methods=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make service file';

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
        return __DIR__ . '/../../../stubs/service.stub';
    }

    /**
     **
     * Map the stub variables present in stub to its value
     *
     * @return array
     */
    public function getStubVariables()
    {
        // $use = "App\Models" . "\\" . $this->argument('name');

        return [
            'NAMESPACE' => 'App\\Services',
            'CLASS_NAME' => $this->getSingularClassName($this->argument('name')),
            // 'USE'               => $use,
            'SINGULAR_VARIABLE' => lcfirst($this->argument('name')),
            'PLURAL_VARIABLE' => lcfirst(Str::plural($this->argument('name'))),
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
        // $contents = file_get_contents($stub);

        // foreach ($stubVariables as $search => $replace)
        // {
        //     $contents = str_replace('$'.$search.'$' , $replace, $contents);
        // }

        $main_stub = __DIR__ . '/../../../stubs/service.stub';

        $upperContents = file_get_contents($main_stub);
        \Log::info('Main stub found');

        foreach ($stubVariables as $search => $replace) {
            $upperContents = str_replace('$' . $search . '$', $replace, $upperContents);
        }

        \Log::info('methods--' . $this->option('methods'));
        $methods = explode(',', $this->option('methods'));

        $methodContents = '';

        foreach ($methods as $method) {
            \Log::info('method--' . $method);

            $stub = __DIR__ . '/../../../stubs/service.' . $method . '.stub';
            \Log::info($method . '-- stub found');

            $stubVariables = $this->getStubVariables();
            $contents = file_get_contents($stub);

            foreach ($stubVariables as $search => $replace) {
                $contents = str_replace('$' . $search . '$', $replace, $contents);
            }

            $methodContents .= PHP_EOL . $contents;
        }

        $fullContents = $upperContents . $methodContents . '}' . PHP_EOL;

        return $fullContents;
    }

    /**
     * Get the full path of generate class
     *
     * @return string
     */
    public function getSourceFilePath()
    {
        return base_path('app/Services') . '/' . $this->getSingularClassName($this->argument('name')) . 'Service.php';
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
