<?php

namespace App\Console\Commands;

use Str;
use Illuminate\Console\Command;
use Illuminate\Support\Pluralizer;
use Illuminate\Filesystem\Filesystem;

class MakeMutationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:mutation {name} {folderName} {--fields=} {--types=} {--required=} {--alias=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to create mutation.';

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
        return __DIR__ . '/../../../stubs/mutation.stub';
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
            'NAMESPACE' => 'App\\GraphQL\\Mutation\\'.$this->argument('folderName'),
            'CLASSNAME' => $this->getSingularClassName($this->argument('name')),
            'SERVICE_CLASSNAME' => $this->argument('folderName'),
            'SERVICE_CLASSNAME_VARIABLE' => $this->getSingularName($this->argument('folderName')),
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

        $main_stub = __DIR__ . '/../../../stubs/mutation.stub';

        $upperContents = file_get_contents($main_stub);
        \Log::info('Main stub found');

        foreach ($stubVariables as $search => $replace) {
            $upperContents = str_replace('$' . $search . '$', $replace, $upperContents);
        }

        $fields = explode(',',$this->option('fields'));
        $dataTypes = explode(',',$this->option('types'));
        $requiredData = explode(',',$this->option('required'));
        $alias = explode(',',$this->option('alias'));

        $fieldCount = count($fields);

        $temp = 'return [';
        for($i = 0 ; $i < $fieldCount ; $i++){
            $rule = '';
            $aliasData = '';
            if($requiredData[$i] == '1'){
                $rule = "'rules' => ['required']";
            }
            if(!empty($alias[$i])){
                $aliasData = "'alias' => '".$alias[$i]."',";
            }
            $temp .=
                "'".$fields[$i]."' => [
                    $aliasData
                    'name' => '".$fields[$i]."',
                    'type' => Type::".$dataTypes[$i]."(),
                    $rule
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
        return base_path('app/GraphQL/Mutation') . '/' .$this->argument('folderName').'/' . $this->argument('name') . '.php';
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
     * Return the Singular Name
     *
     * @return string
     */
    public function getSingularName($name,$is_lower = false)
    {

        return lcfirst($name);

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
