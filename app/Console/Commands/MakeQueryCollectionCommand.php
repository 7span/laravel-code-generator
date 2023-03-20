<?php

namespace App\Console\Commands;

use Str;
use Illuminate\Console\Command;
use Illuminate\Support\Pluralizer;
use Illuminate\Filesystem\Filesystem;

class MakeQueryCollectionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:query-collection {name} {--fields=} {--types=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make query collection file';

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
        return __DIR__ . '/../../../stubs/query-collection.stub';
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
            'NAMESPACE' => 'App\\GraphQL\\Query\\'.str_replace('CollectionQuery','',$this->getSingularClassName($this->argument('name'))),
            'CLASSNAME' => $this->getSingularClassName($this->argument('name')),
            'SERVICE_CLASSNAME' => str_replace('CollectionQuery','',$this->getSingularClassName($this->argument('name'))),
            'SERVICE_CLASSNAME_VARIABLE' => strtolower(str_replace('CollectionQuery','',$this->getSingularClassName($this->argument('name')))),
            'MODEL_NAME' => str_replace('CollectionQuery','',$this->getSingularClassName($this->argument('name'))),
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

        $main_stub = __DIR__ . '/../../../stubs/query-collection.stub';

        $upperContents = file_get_contents($main_stub);
        \Log::info('Main stub found');

        foreach ($stubVariables as $search => $replace) {
            $upperContents = str_replace('$' . $search . '$', $replace, $upperContents);
        }

        $fields = explode(',',$this->option('fields'));
        $dataTypes = explode(',',$this->option('types'));

        $fieldsArr = [];
        $fieldCount = count($fields);
        $modelName = str_replace('Type','',$this->getSingularClassName($this->argument('name')));

        $temp = 'return [';
        for($i = 0 ; $i < $fieldCount ; $i++){
            $temp .= "'".$fields[$i]."' => [
                    'name' => '".$fields[$i]."',
                    'type' => Type::".$dataTypes[$i]."()
                ],";
        }

        $search = 'return [';
        $upperContents = str_replace($search, $temp, $upperContents);
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
        return base_path('app/GraphQL/Query') . '/' . str_replace('CollectionQuery','',$this->getSingularClassName($this->argument('name'))).'/'.$this->getSingularClassName($this->argument('name')) . '.php';
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
