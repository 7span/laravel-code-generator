<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class MakeRequest extends GeneratorCommand
{
    protected $name = 'make:request-validation';
    protected $description = 'Create a new form request class with validation rules';
    protected $type = 'Request';
    protected $generatedRules = '';

    public function handle()
    {
        $this->generatedRules = $this->buildRules();
        // - Qualifying the class name
        // - Determining the path
        // - Making the directory
        // - Building the class content (which calls replaceNamespace and our replaceClass)
        // - Writing the file
        $result = parent::handle();

        if ($result === false && !$this->option('force')) {
            return SymfonyCommand::FAILURE;
        }

        return SymfonyCommand::SUCCESS;
    }

    protected function getStub()
    {
        return __DIR__ . '/../../stubs/request.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Requests';
    }

    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the request class'],
            ['fields', InputArgument::IS_ARRAY, 'List of fields and their types (e.g., name:string email:email)'],
        ];
    }
    protected function replaceClass($stub, $name)
    {
        $stub = parent::replaceClass($stub, $name);

        return str_replace(
            ['{{ class }}', '{{ rules }}'],
            [class_basename($name), $this->generatedRules],
            $stub
        );
    }
    protected function buildRules(): string
    {
        $rules = [];
        $fieldsArgument = $this->argument('fields');

        if (empty($fieldsArgument)) {
            $this->comment('No fields provided. Generating an empty rules array.');
            return '';
        }

        foreach ($fieldsArgument as $field) {
            $parts = explode(':', $field, 2);
            $name = trim($parts[0]);
            $typeDefinition = isset($parts[1]) ? trim($parts[1]) : 'string';
            if (str_contains($typeDefinition, '|')) {
                $validation = $typeDefinition;
            } else {
                $validation = match (strtolower($typeDefinition)) {
                    'string' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255',
                    'integer', 'int' => 'required|integer',
                    'boolean', 'bool' => 'required|boolean',
                    'date' => 'required|date',
                    'datetime' => 'required|date_format:Y-m-d H:i:s',
                    'numeric' => 'required|numeric',
                    'url' => 'required|url',
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                    'file' => 'required|file|max:10240',
                    'password' => 'required|string|min:8|confirmed',
                    'phone' => 'required|string|regex:/^[0-9\s\-\+\(\)]*$/',
                    'array' => 'required|array',
                    'json' => 'required|json',
                    'ip' => 'required|ip',
                    'uuid' => 'required|uuid',
                    // Add more types as needed
                    default => 'required|string',
                };
            }
            $rules[] = "'$name' => '$validation'";
        }
        return implode(",\n            ", $rules);
    }
}