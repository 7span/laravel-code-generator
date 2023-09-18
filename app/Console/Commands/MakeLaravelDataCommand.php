<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Pluralizer;
use Illuminate\Filesystem\Filesystem;


class MakeLaravelDataCommand extends Command
{
     /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:laravel-data {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a laraveldata file';


        /**
     * Create a new command instance.
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }


     /**
     * Filesystem instance
     *
     * @var Filesystem
     */
    protected $files;

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
        $contents = file_get_contents($stub);
      
        foreach ($stubVariables as $search => $replace) {

            $contents = str_replace('$' . $search . '$', $replace, $contents);
        }
        return $contents;
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
            'NAMESPACE' => 'App\\Http\\Data',
            'CLASS_NAME' => $this->getSingularClassName($this->argument('name')),
        ];
    }


    public function getStubPath()
    {
        return __DIR__ . '/../../../stubs/laravel-data.stub';
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

    public function getSourceFilePath()
    {
        return base_path('app/Http/Data') . '/' . $this->getSingularClassName($this->argument('name')) . 'LaravelData.php';
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
}
