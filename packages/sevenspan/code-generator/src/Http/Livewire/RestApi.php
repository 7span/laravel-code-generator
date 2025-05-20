<?php

namespace Sevenspan\CodeGenerator\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;
use Sevenspan\CodeGenerator\Console\Commands\MakeRequest;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class RestApi extends Component
{
    public array $relationData = [];
    public array $fieldsData = [];
    public array $notificationData = [];
    public $generalError = ''; 
    protected $signature = '';

    // Modal visibility 
    public $isAddRelModalOpen = false;
    public $isRelDeleteModalOpen = false;
    public $isRelEditModalOpen = false;
    public $isAddFieldModalOpen = false;
    public $isDeleteFieldModalOpen = false;
    public $isEditFieldModalOpen = false;
    public $isNotificationModalOpen = false;

    // General form inputs
    public $modelName;

    public $relations, $relationId, $fields, $fieldId;

    // Relationship form fields
    public $related_model, $relation_type, $second_model, $foreign_key, $local_key;

    // Field properties
    public $data_type, $column_name, $column_validation, $add_scope;

    // Notification properties
    public $class_name, $data, $subject, $body;

    // Method checkboxes
    public $index = false;
    public $store = false;
    public $show = false;
    public $update = false;
    public $destroy = false;
    public $modelFile = false;
    public $migrationFile = false;
    public $softDeleteFile = false;
    public $crudFile = false;
    public $serviceFile = false;
    public $notificationFile = false;
    public $resourceFile = false;
    public $requestFile = false;
    public $overwriteFiles = false;
    public $observerFile = false;
    public $factoryFile = false;
    public $policyFile = false;

    // Trait checkboxes
    public $ApiResponse = false;
    public $BaseModel = false;
    public $BootModel = false;
    public $PaginationTrait = false;
    public $ResourceFilterable = false;
    public $HasUuid = false;
    public $HasUserAction = false;

    public $errorMessage = "";
    public $successMessage = '';
    public $generatedFiles = [];
    //validation rules
    protected $rules = [
        'modelName' => 'required|regex:/^[A-Z][a-z]+$/',
        'related_model' => 'required|regex:/^[A-Z][a-z]+$/',
        'relation_type' => 'required',
        'second_model' => 'required|regex:/^[A-Z][a-z]+$/',
        'foreign_key' => 'required',
        'local_key' => 'required',
        'data_type' => 'required',
        'column_name' => 'required|regex:/^[A-Za-z]+$/',
        'column_validation' => 'required',
        'add_scope' => 'required',
        'class_name' => 'required|regex:/^[A-Z][A-Za-z]+$/',
        'data' => 'required|regex:/^\\s*\\[\\s*(["\']?[A-Za-z_]+["\']?\\s*=>\\s*\\d+\\s*(?:,\\s*["\']?[A-Za-z]+["\']?\\s*=>\\s*\\d+)*)?\\s*\\]$/',
        'subject' => 'required|regex:/^[A-Za-z ]+$/',
        'body' => 'required|regex:/^[A-Za-z ]+$/',
    ];
        public $messages = [
        'modelName.regex' => 'The Model Name must start with an uppercase letter and contain only letters.',
        'related_model.regex' => 'The Model Name must start with an uppercase letter and contain only letters.',
        'data.array' => 'The Data field must be a valid array.', 
    ];


    public function updatedCrudFile(): void
    {
        if ($this->crudFile) {
            $this->index = true;
            $this->store = true;
            $this->show = true;
            $this->update = true;
            $this->destroy = true;
        } else {
            $this->index = false;
            $this->store = false;
            $this->show = false;
            $this->update = false;
            $this->destroy = false;
        }
    }
     //Relations Handling
    public function openDeleteModal($id): void
    {
        $this->relationId = $id;
        $this->isRelDeleteModalOpen = true;
    }

    public function deleteRelation(): void
    {
        $this->relationData = array_filter($this->relationData, function ($relation) {
            return $relation['id'] !== $this->relationId;
        });
        $this->isRelDeleteModalOpen = false;
    }

    public function openEditRelationModal($relationId): void
    {
        $this->relationId = $relationId;
        $this->isRelEditModalOpen = true;
        $relation = collect($this->relationData)->firstWhere('id', $relationId);
        if ($relation) {
            $this->fill($relation);
        }
    }

    public function addRelation(): void
    {
        $rules= [
            'related_model' => $this->rules['related_model'],
            'relation_type' => $this->rules['relation_type'],
            'foreign_key' => $this->rules['foreign_key'],
            'local_key' => $this->rules['local_key'],
        ];
        if (in_array($this->relation_type, ['Has One Through', 'Has Many Through'])) {
            $rules['second_model'] = $this->rules['second_model'];
        }
        $this->validate($rules);

        if ($this->relationId) { // If we're editing
            $this->relationData = collect($this->relationData)->map(function ($relation) {
            if ($relation['id'] === $this->relationId) {
                return [
                    'id' => $this->relationId,
                    'related_model' => $this->related_model,
                    'relation_type' => $this->relation_type,
                    'second_model' => $this->second_model,
                    'foreign_key' => $this->foreign_key,
                    'local_key' => $this->local_key,
                ];
            }
            return $relation;
        })->toArray();
    } else { // If we're adding a new relation
        $this->relationData[] = [
            'id' => Str::random(8),
            'related_model' => $this->related_model,
            'second_model' => $this->second_model,
            'relation_type' => $this->relation_type,
            'foreign_key' => $this->foreign_key,
            'local_key' => $this->local_key,
        ];
    }
        $this->isAddRelModalOpen = false;
        $this->isRelEditModalOpen = false;
        $this->resetForm();
        $this->relationId = null;
    }

    // Fields Handling
    public function saveField()
    {
        // Check if column name already exists
        $columnExists = collect($this->fieldsData)->contains(function ($field) {
            return $field['column_name'] === $this->column_name && 
                   ($this->fieldId ? $field['id'] !== $this->fieldId : true);
        });

        if ($columnExists) {
            $this->addError('column_name', 'This column name already exists.');
            return;
        }

        $this->validate([
            'data_type' => $this->rules['data_type'],
            'column_name' => $this->rules['column_name'],
            'column_validation' => $this->rules['column_validation'],
            'add_scope' => $this->rules['add_scope'],
        ]);

        if ($this->fieldId) { // If we are editing
            $this->fieldsData = collect($this->fieldsData)->map(function ($field) {
                if ($field['id'] === $this->fieldId) {
                    return [
                        'id' => $this->fieldId,
                        'data_type' => $this->data_type,
                        'column_name' => $this->column_name,
                        'column_validation' => $this->column_validation,
                        'add_scope' => $this->add_scope,
                    ];
                }
                return $field;
            })->toArray();
        } else { // If we are adding a new field
            $this->fieldsData[] = [
                'id' => Str::random(8),
                'data_type' => $this->data_type,
                'column_name' => $this->column_name,
                'column_validation' => $this->column_validation,
                'add_scope' => $this->add_scope,
            ];
        }
        $this->isAddFieldModalOpen = false;
        $this->isEditFieldModalOpen = false;
        $this->fieldId = null;
        $this->resetForm();
    }
    //open editfieldmodal
    public function openEditFieldModal($fieldId)
    {
        $this->fieldId = $fieldId;
        $this->isEditFieldModalOpen = true;
        $field = collect($this->fieldsData)->firstWhere('id', $fieldId);
        if ($field) {
            $this->fill($field);
        }
    }
 //open delete field modal
    public function openDeleteFieldModal($id)
    {
        $this->fieldId = $id;
        $this->isDeleteFieldModalOpen = true;
    }
 //delete field from table
    public function deleteField(): void
    {
        $this->fieldsData = array_filter($this->fieldsData, function ($field) {
            return $field['id'] !== $this->fieldId;
        });
        $this->isDeleteFieldModalOpen = false;
        $this->fieldId = null;
        $this->resetForm();
    }
    
    // Notification
    public function updatedNotificationFile(): void
    {
        if ($this->notificationFile) {
            $this->isNotificationModalOpen = true;
        }
    }

    public function saveNotification(): void
    {
        $this->validate([
            'class_name' => $this->rules['class_name'],
            'data' => $this->rules['data'],
            'subject' => $this->rules['subject'],
            'body' => $this->rules['body'],
        ]);
        $this->notificationData[] = [
            'class_name' => $this->class_name,
            'data' => $this->data,
            'subject' => $this->subject,
            'body' => $this->body,
        ];
        $this->isNotificationModalOpen = false;
    }

    // live Validation
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function check()
    {
        $this->errorMessage = "";
        
        // Check if both fields and methods are missing
        if (empty($this->fieldsData) && !($this->index || $this->store || $this->show || $this->destroy || $this->update)) {
            $this->errorMessage = "Please add at least one field and select at least one method.";
            return false;
        }

        // Check only for fields
        if (empty($this->fieldsData)) {
            $this->errorMessage = "Please add at least one field.";
            return false;
        }

        // Check only for methods
        if (!($this->index || $this->store || $this->show || $this->destroy || $this->update)) {
            $this->errorMessage = "Please select at least one method.";
            return false;
        }

        return true;
    }
  // reset form fields
    public function resetForm()
    {
        $this->reset([
            'related_model',
            'relation_type',
            'second_model',
            'foreign_key',
            'local_key',
            'data_type',
            'column_name',
            'column_validation',
            'add_scope',
            'fieldId'
        ]);
        $this->resetErrorBag();
    }

    //pass form data and calls commands
    public function save(): void
    {
        $relationMap = [
            'One to One' => 'hasOne',
            'One to Many' => 'hasMany',
            'Many to Many' => 'belongsToMany',
            'Has One Through' => 'hasOneThrough',
            'Has Many Through' => 'hasManyThrough',
            'One To One (Polymorphic)' => 'morphOne',
            'One To Many (Polymorphic)' => 'morphMany',
            'Many To Many (Polymorphic)' => 'morphToMany',
        ];

        // Validate model name
        $validModelName = $this->validate([
            'modelName' => $this->rules['modelName'],
        ]);

        // Check fields and methods
        if (!$this->check()) {
            session()->flash('error', $this->errorMessage);
            return;
        }

        $modelName = $validModelName['modelName'];
        $relations = $this->relationData;
        $fields = $this->fieldsData;
        $notification = $this->notificationData;
        $files = [
            'model' => $this->modelFile,
            'migration' => $this->migrationFile,
            'softDelete' => $this->softDeleteFile,
            'crudFile' => $this->crudFile,
            'service' => $this->serviceFile,
            'notification' => $this->notificationFile,
            'resource' => $this->resourceFile,
            'request' => $this->requestFile,
            'observer' => $this->observerFile,
            'policy' => $this->policyFile,
            'factory' => $this->factoryFile
        ];
        $methods = [
            'index' => $this->index,
            'store' => $this->store,
            'show' => $this->show,
            'update' => $this->update,
            'destroy' => $this->destroy,
        ];
        $overwrite = $this->overwriteFiles;

        // Generate files
        $this->generatedFiles = []; // Reset generated files array
        $fieldString = collect($fields)->pluck('column_name')->implode(', ');

        $relations = implode(', ', array_map(function ($relationData) use ($relationMap) {
            $method = $relationMap[$relationData['relation_type']] ?? 'unknown';
            return $relationData['related_model'] . ':' . $method;
        }, $relations));

        $methods = implode(',', array_keys(array_filter($methods))); 
        $softDelete = $files['softDelete'];
        $factory = $files['migration'];
        // Get selected traits
        $selectedTraits = [];
        if ($this->ApiResponse) $selectedTraits[] = 'ApiResponse';
        if ($this->BaseModel) $selectedTraits[] = 'BaseModel';
        if ($this->BootModel) $selectedTraits[] = 'BootModel';
        if ($this->PaginationTrait) $selectedTraits[] = 'PaginationTrait';
        if ($this->ResourceFilterable) $selectedTraits[] = 'ResourceFilterable';
        if ($this->HasUuid) $selectedTraits[] = 'HasUuid';
        if ($this->HasUserAction) $selectedTraits[] = 'HasUserAction';

        $traitsString = implode(',', $selectedTraits);

        $selectedMethods = [];
        if ($this->index) $selectedMethods[] = 'index';
        if ($this->store) $selectedMethods[] = 'store';
        if ($this->show) $selectedMethods[] = 'show';
        if ($this->update) $selectedMethods[] = 'update';
        if ($this->destroy) $selectedMethods[] = 'destroy';

        // Model command
        if($files['model']) {
            Artisan::call('codegenerator:model', [
            'modelName' => $modelName,
            '--fields' => $fieldString,
            '--relations' => $relations,
            '--methods' => implode(',', $selectedMethods),
            '--softDelete' => $softDelete,
            '--factory' => $factory,
            '--traits' => $traitsString,
            '--overwrite' => $overwrite,
        ]);
      //  $this->generatedFiles[] = "Model: {$modelName}";
    }
    //------------------------------------------
    //controller command 
    //-----------------------------------------
        Artisan::call('codegenerator:controller', [
            'modelName' => $modelName,
            '--methods' => implode(',', $selectedMethods),
            '--service' => $files['service'],
            '--resource' => $files['resource'],
            '--request' => $files['request'],
            '--overwrite' => $overwrite
        ]);
        $this->generatedFiles[] = "Controller: {$modelName}Controller";

        //------------------------------------------
        //resource command 
        //-----------------------------------------
        if($files['resource']) {
        Artisan::call('codegenerator:resource', [
            'modelName' => $modelName,
            '--overwrite' => $overwrite
        ]);
    }
        //------------------------------------------
        //factory command 
        //-----------------------------------------
        $fieldString = implode(',', array_map(function ($fields) {
                return $fields['column_name'] . ':' . $fields['data_type'];
            }, $fields));

    if($files['factory']) {
        Artisan::call('codegenerator:factory', [
            'modelName' => $modelName,
            '--fields' => $fieldString,
            '--overwrite' => $overwrite
        ]);
    }
        
        //------------------------------------------
        //
        //migration command 
        //-----------------------------------------
        if($files['migration']) {
          Artisan::call('codegenerator:migration', [
            'modelName' => $modelName,
            '--fields' => $fieldString,
            '--softdelete' => $files['softDelete'],
            '--overwrite' => $overwrite
        ]);
    }
        //------------------------------------------
        //notification command 
        //-----------------------------------------
            if($files['notification']) {
                Artisan::call('codegenerator:notification', [
                    'className' => $this->notificationData['class_name'] ?? $modelName . 'Notification',
                    '--modelName' => $modelName,
                    '--data' => $this->notificationData['data'] ?? '',
                    '--body' => $this->notificationData['body'] ?? '',
                    '--subject' => $this->notificationData['subject'] ?? '',
                    '--overwrite' => $overwrite
                ]);
            }
        //----------------------------------------
        //request command 
        //----------------------------------------

        $ruleString = implode(',', array_map(function($field) {
            return $field['column_name'] . ':' . $field['column_validation'];
        }, $fields));

        if($files['request']) {
            Artisan::call('codegenerator:request', [
                $modelName,
                '--rules' => $ruleString,
                '--overwrite' => $overwrite
            ]);

        }
         //SERVICE command 
         if($files['service']) {
            Artisan::call('codegenerator:service',[
                'modelName' => $modelName,
                '--overwrite' => $overwrite
            ]);
        }
        //------------------------------------------
        //observer command 
        //-----------------------------------------   
        if($files['observer'])  {
            Artisan::call('codegenerator:observer',[
                'modelName' => $modelName,
                '--overwrite' => $overwrite
            ]);
        }
        //------------------------------------------
        //policy command 
        //-----------------------------------------
    if($files['policy']) {
        Artisan::call('codegenerator:policy',[
            'modelName' => $modelName,
            '--overwrite' => $overwrite
        ]);
    }
        // traits  
            $source = __DIR__ . '/../../TraitsLibrary/Traits';
            $destination = app_path(config('code_generator.trait_path','Traits'));

            // Copy directory if it doesn't exist yet or overwrite if needed
            if (File::exists($source)) {
                File::ensureDirectoryExists($destination);
                File::copyDirectory($source, $destination);
                Log::info('✔ Traits copied successfully to app/Traits.');
            } else {
                $this->error('Traits source folder not found: ' . $source);
            }

            // Copy traits if any are selected
            if (!empty($selectedTraits)) {
                $source = __DIR__ . '/../../TraitsLibrary/Traits';
                $destination = app_path(config('code_generator.trait_path','Traits'));

                // Copy directory if it doesn't exist yet or overwrite if needed
                if (File::exists($source)) {
                    File::ensureDirectoryExists($destination);
                    File::copyDirectory($source, $destination);
                    Log::info('✔ Traits copied successfully to app/Traits.');
                } else {
                    $this->error('Traits source folder not found: ' . $source);
                }
            }
        
        // Show success message
        $this->successMessage = 'Successfully generated Files';
        session()->flash('success', $this->successMessage);
    }

    public function render()
    {
        return view('code-generator::livewire.rest-api');
    }
}