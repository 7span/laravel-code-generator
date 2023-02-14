<?php

namespace App\Console\Commands;

use Str;
use Illuminate\Console\Command;
use Illuminate\Support\Pluralizer;
use Illuminate\Filesystem\Filesystem;

class MakeAdminControllerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin-controller API/V1/Admin/{name}Controller {--methods=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make api controller file';

    /**
     * Filesystem instance
     * @var Filesystem
     */
    protected $files;

    /**
     * Create a new command instance.
     * @param Filesystem $files
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

        if (!$this->files->exists($path)) {
            $this->files->put($path, $contents);
            $this->info("File : {$path} created");
        } else {
            $this->info("File : {$path} already exits");
        }
    }

    /**
     * Return the stub file path
     * @return string
     *
     */
    public function getStubPath()
    {
        return __DIR__ . '/../../../stubs/admin-controller.stub';
    }

    /**
    **
    * Map the stub variables present in stub to its value
    *
    * @return array
    *
    */
    public function getStubVariables()
    {
        // $use = "App\Models" . "\\" . $this->argument('name');
        
        return [
            'NAMESPACE'         => 'App\\Http\\Controllers\\Api\\V1\\Admin',
            'CLASS_NAME'        => $this->getSingularClassName($this->argument('name')),
            // 'USE'               => $use,
            'SINGULAR_VARIABLE'          => Str::singular(strtolower($this->argument('name'))),
            'PLURAL_VARIABLE'          => Str::plural(strtolower($this->argument('name'))),
        ];
    }

    /**
     * Get the stub path and the stub variables
     *
     * @return bool|mixed|string
     *
     */
    public function getSourceFile()
    {
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables());
    }

    /**
     * Replace the stub variables(key) with the desire value
     *
     * @param $stub
     * @param array $stubVariables
     * @return bool|mixed|string
     */
    public function getStubContents($stub , $stubVariables = [])
    {
        // $contents = file_get_contents($stub);
        
        // foreach ($stubVariables as $search => $replace)
        // {
        //     $contents = str_replace('$'.$search.'$' , $replace, $contents);
        // }

        // $stub = base_path('stubs/controller.stub');
        $main_stub = __DIR__ . '/../../../stubs/admin-controller.stub';

        $upperContents = file_get_contents($main_stub);
        \Log::info('Main stub found');
        
        foreach ($stubVariables as $search => $replace)
        {
            $upperContents = str_replace('$'.$search.'$' , $replace, $upperContents);
        }
        
        \Log::info('methods--' . $this->option('methods'));
        $methods = explode(",",$this->option('methods'));

        $methodContents = '';
        
        foreach($methods as $method) {
            \Log::info('method--' . $method);
            if($method == "show") {
                $className = $stubVariables['CLASS_NAME'];
                $string_to_replace = 'use App\Http\Controllers\Controller;';
                $replace_with = $string_to_replace . PHP_EOL . 'use App\Http\Resources' . '\\' . $className . '\Resource as ' . $className . 'Resource;';
                $upperContents = str_replace($string_to_replace, $replace_with, $upperContents);
            } else if($method == "index") {
                $className = $stubVariables['CLASS_NAME'];
                $string_to_replace = $className . 'Request;';
                $replace_with = $string_to_replace . PHP_EOL . 'use App\Http\Resources' . '\\' . $className . '\Collection as ' . $className . 'Collection;';
                $upperContents = str_replace($string_to_replace, $replace_with, $upperContents);
            }
            
            // $stub = base_path('stubs/controller.' . $method . '.stub');
            $stub = __DIR__ . '/../../../stubs/controller.' . $method . '.stub';
            \Log::info($method . '-- stub found');

            $stubVariables = $this->getStubVariables();
            $contents = file_get_contents($stub);
            
            foreach ($stubVariables as $search => $replace)
            {
                $contents = str_replace('$'.$search.'$' , $replace, $contents);
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
        \Log::info('File bne 6e');
        \Log::info(base_path('app/Http/Controllers/API/V1/Admin') .'/' .$this->getSingularClassName($this->argument('name')) . 'Controller.php');
        return base_path('app/Http/Controllers/API/V1/Admin') .'/' .$this->getSingularClassName($this->argument('name')) . 'Controller.php';
    }

    /**
     * Return the Singular Capitalize Name
     * @param $name
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

    public function replace_string_in_file($filename, $string_to_replace, $replace_with){
        $content=file_get_contents($filename);
        $content_chunks=explode($string_to_replace, $content);
        $content=implode($replace_with, $content_chunks);
        file_put_contents($filename, $content);
    }

}
