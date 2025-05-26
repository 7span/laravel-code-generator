<?php

namespace Sevenspan\CodeGenerator\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class RestApi extends Component
{
    // Data properties
    public array $relationData = [];
    public array $fieldsData = [];
    public array $notificationData = [];

    public array $tableNames = [];

    public $fieldNames = [];
    public $generalError = '';
    public $errorMessage = "";
    public $successMessage = '';

    public $isForeignKey = false;
    public $foreignModelName = '';
    public $referencedColumn = '';


    // Modal visibility properties
    public $isAddRelModalOpen = false;
    public $isRelDeleteModalOpen = false;
    public $isRelEditModalOpen = false;
    public $isAddFieldModalOpen = false;
    public $isDeleteFieldModalOpen = false;
    public $isEditFieldModalOpen = false;
    public $isNotificationModalOpen = false;

    // Form inputs
    public $modelName;
    public $relations, $relationId, $fields, $fieldId;

    // Relationship form fields
    public $related_model, $relation_type, $second_model, $foreign_key, $local_key;

    // Field properties
    public $data_type, $column_name, $column_validation;

    // Notification properties
    public $class_name, $data, $subject, $body;

    // Method checkboxes
    public $index = false;
    public $store = false;
    public $show = false;
    public $update = false;
    public $destroy = false;

    // File generation options
    public $modelFile = false;
    public $migrationFile = false;
    public $softDeleteFile = false;
    public $crudFile = false;
    public $serviceFile = false;
    public $notificationFile = false;
    public $resourceFile = false;
    public $requestFile = false;
    public $traitFiles = false;
    public $overwriteFiles = false;
    public $observerFile = false;
    public $factoryFile = false;
    public $policyFile = false;

    // Trait checkboxes
    public $BootModel = false;
    public $PaginationTrait = false;
    public $ResourceFilterable = false;
    public $HasUuid = false;
    public $HasUserAction = false;

    // Validation rules
    protected $rules = [
        'modelName' => 'required|regex:/^[A-Z][a-z]+$/',
        'related_model' => 'required|regex:/^[A-Z][a-z]+$/',
        'relation_type' => 'required',
        'second_model' => 'required|regex:/^[A-Z][a-z]+$/',
        'foreign_key' => 'required|regex:/^[a-zA-Z][a-zA-Z0-9_]/',
        'local_key' => 'required|regex:/^[a-z_]+$/',
        'data_type' => 'required',
        'column_name' => 'required|regex:/^[a-z_]+$/',
        'column_validation' => 'required',
        'class_name' => 'required|regex:/^[A-Z][A-Za-z]+$/',
        'data' => 'required|regex:/^[A-Za-z0-9]+:[A-Za-z0-9]+(?:,[A-Za-z0-9]+:[A-Za-z0-9]+)*$/',
        'subject' => 'required|regex:/^[A-Za-z ]+$/',
        'body' => 'required|regex:/^[A-Za-z ]+$/',
        'foreignModelName' => 'required|regex:/^[a-z0-9_]+$/',
    ];

    public $messages = [
        'modelName.regex' => 'The Model Name must start with an uppercase letter and contain only letters.',
        'related_model.regex' => 'The Model Name must start with an uppercase letter and contain only letters.',
    ];

    public function render()
    {
        return view('code-generator::livewire.rest-api');
    }

    // Add persist property to maintain state
    protected $persist = [
        'modelName',
        'fieldsData',
        'relationData',
        'notificationData',
        'index',
        'store',
        'show',
        'update',
        'destroy',
        'modelFile',
        'migrationFile',
        'softDeleteFile',
        'serviceFile',
        'notificationFile',
        'resourceFile',
        'requestFile',
        'overwriteFiles',
        'observerFile',
        'factoryFile',
        'policyFile',
        'BootModel',
        'PaginationTrait',
        'ResourceFilterable',
        'HasUuid',
        'HasUserAction'
    ];

    public function updatedIsForeignKey($value)
    {
        if (!$value) {
            // Checkbox was unchecked - clear related fields
            $this->foreignModelName = '';
            $this->referencedColumn = '';
        }
    }

    // Add mount method to restore state
     public function mount()
     {
         if (session()->has('form_data')) {
             $formData = session('form_data');
             foreach ($formData as $key => $value) {
                 if (property_exists($this, $key)) {
                     $this->$key = $value;
                 }
             }
         }
         $this->loadMigrationTableNames();
     }

    // Add dehydrate method to store state before navigation
     public function dehydrate()
     {
         session()->put(
             'form_data',
         collect($this->persist)
                 ->mapWithKeys(fn($property) => [$property => $this->$property])
                 ->toArray()
         );
     }


    /**
     * Live validation for form fields
     */
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    /**
     * Update notification file checkbox state and open modal if checked
     */
    public function updatedNotificationFile(): void
    {
        if ($this->notificationFile) {
            $this->isNotificationModalOpen = true;
        }
    }

    /**
     * Validate fields and methods
     */
    public function validateFieldsAndMethods()
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

    //resets form fields
    public function resetForm()
    {
        $this->reset([
            'related_model',
            'relation_type',
            'second_model',
            'foreign_key',
            'local_key',
            'data_type',
            'isForeignKey',
            'column_name',
            'column_validation',
            'fieldId',
            'isForeignKey',
            'foreignModelName',
            'referencedColumn'

        ]);
        $this->resetErrorBag();
    }


    public function addRelation(): void
    {
        $rules = [
            'related_model' => $this->rules['related_model'],
            'relation_type' => $this->rules['relation_type'],
            'foreign_key' => $this->rules['foreign_key'],
            'local_key' => $this->rules['local_key'],
        ];

        // Add second_model rule if needed
        if (in_array($this->relation_type, ['Has One Through', 'Has Many Through'])) {
            $rules['second_model'] = $this->rules['second_model'];
        }

        $this->validate($rules);

        $relationData = [
            'related_model' => $this->related_model,
            'relation_type' => $this->relation_type,
            'foreign_key' => $this->foreign_key,
            'local_key' => $this->local_key,
        ];

        if (isset($rules['second_model'])) {
            $relationData['second_model'] = $this->second_model;
        }

        // Update or add relation
        if ($this->relationId) {
            foreach ($this->relationData as &$relation) {
                if ($relation['id'] === $this->relationId) {
                    $relation = array_merge(['id' => $this->relationId], $relationData);
                    break;
                }
            }
            unset($relation);  // break reference
        } else {
            $this->relationData[] = ['id' => Str::random(8)] + $relationData;
        }
        $this->isAddRelModalOpen = false;
        $this->isRelEditModalOpen = false;
        $this->reset(['related_model', 'relation_type', 'second_model', 'foreign_key', 'local_key',]);
        $this->relationId = null;
    }

    public function openEditFieldModal($fieldId): void
    {
        $this->fieldId = $fieldId;
        $field = collect($this->fieldsData)->firstWhere('id', $fieldId);

        if ($field) {
            $this->column_name = $field['column_name'] ?? '';
            $this->data_type = $field['data_type'] ?? '';
            $this->column_validation = $field['column_validation'] ?? '';
            $this->isForeignKey = (bool) ($field['isForeignKey'] ?? false);
            $this->foreignModelName = $field['foreignModelName'] ?? '';
            $this->referencedColumn = $field['referencedColumn'] ?? '';
        }

        $this->isEditFieldModalOpen = true;
    }

    public function openDeleteFieldModal($id): void
    {
        $this->fieldId = $id;
        $this->isDeleteFieldModalOpen = true;
    }

    public function deleteField(): void
    {
        $this->fieldsData = array_filter($this->fieldsData, function ($field) {
            return $field['id'] !== $this->fieldId;
        });
        $this->isDeleteFieldModalOpen = false;
    }

    public function saveField(): void
    {

        // Check for duplicate column name, excluding the current edited field by ID
        $columnExists = false;
        foreach ($this->fieldsData as $field) {
            if (
                $field['column_name'] === $this->column_name &&
                (!$this->fieldId || $field['id'] !== $this->fieldId)
            ) {
                $columnExists = true;
                break;
            }
        }


        if ($columnExists) {
            $this->addError('column_name', 'You have already taken this column');
            return;
        }


        $rulesToValidate = [
            'data_type' => $this->rules['data_type'],
            'column_name' => $this->rules['column_name'],
            'column_validation' => $this->rules['column_validation'],
        ];

        if ($this->isForeignKey) {
            $rulesToValidate['foreignModelName'] = $this->rules['foreignModelName'];
            $rulesToValidate['referencedColumn'] = $this->rules['local_key'];
        }

        $this->validate($rulesToValidate);


        $fieldData = [
            'data_type' => $this->data_type,
            'column_name' => $this->column_name,
            'column_validation' => $this->column_validation,
            'isForeignKey' => $this->isForeignKey ?? false, // default fallback
            'foreignModelName' => $this->foreignModelName,
            'referencedColumn' => $this->referencedColumn,
        ];

        // Update existing field or add new one
        if ($this->fieldId) {
            foreach ($this->fieldsData as &$field) {
                if ($field['id'] === $this->fieldId) {
                    $field = ['id' => $this->fieldId] + $fieldData;
                    break;
                }
            }
            unset($field); // break reference
        } else {
            $this->fieldsData[] = array_merge(['id' => Str::random(8)], $fieldData);
        }
        // dd($this->fieldsData);
        $this->isAddFieldModalOpen = false;
        $this->isEditFieldModalOpen = false;
        $this->fieldId = null;
        $this->reset(['column_name', 'data_type', 'column_validation', 'isForeignKey', 'foreignModelName', 'referencedColumn']);
    }

    public function saveNotification(): void
    {
        $this->validate([
            'class_name' => $this->rules['class_name'],
            'data' => $this->rules['data'],
            'subject' => $this->rules['subject'],
            'body' => $this->rules['body'],
        ]);

        // Store notification data
        $this->notificationData = [
            [
                'class_name' => $this->class_name,
                'data' => $this->data,
                'subject' => $this->subject,
                'body' => $this->body,
            ]
        ];

        $this->isNotificationModalOpen = false;
        $this->reset(['class_name', 'data', 'subject', 'body']);
    }

    /**
     * Validate inputs before generation
     */
    private function validateInputs(): bool
    {
        // Validate model name
        $this->validate(['modelName' => $this->rules['modelName']]);

        // Check if model exists and overwrite is not checked
        $modelPath = app_path('Models/' . $this->modelName . '.php');
        if (File::exists($modelPath) && !$this->overwriteFiles) {
            $this->errorMessage = "Model {$this->modelName} already exists. Please check 'Overwrite Files' if you want to overwrite it.";
            session()->flash('error', $this->errorMessage);
            $this->dispatch('show-toast', ['message' => $this->errorMessage, 'type' => 'error']);
            return false;
        }

        // Check fields and methods validation
        if (!$this->validateFieldsAndMethods()) {
            session()->flash('error', $this->errorMessage);
            return false;
        }

        return true;
    }

    public function save(): void
    {
        try {
            // Validate all inputs first
            if (!$this->validateInputs()) {
                return;
            }
            // Generate files
            $this->generateFiles();
            session()->flash('success', 'Files generated Successfully!');

            // Reset form
            $this->reset();
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            session()->flash('error', $e->getMessage());
            $this->dispatch('show-toast', ['message' => $e->getMessage(), 'type' => 'error']);
        }
    }

    /**
     * Generate all selected files
     */
    private function generateFiles(): void
    {
        $selectedTraits = array_filter([
            'ApiResponser',
            'BaseModel',
            $this->BootModel ? 'BootModel' : null,
            $this->PaginationTrait ? 'PaginationTrait' : null,
            $this->ResourceFilterable ? 'ResourceFilterable' : null,
            $this->HasUuid ? 'HasUuid' : null,
            $this->HasUserAction ? 'HasUserAction' : null,
        ]);
        // Relation mapping
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

        $modelName = $this->modelName;

        // Prepare selected methods
        $selectedMethods = array_filter([
            $this->index ? 'index' : null,
            $this->store ? 'store' : null,
            $this->show ? 'show' : null,
            $this->update ? 'update' : null,
            $this->destroy ? 'destroy' : null,
        ]);

        // Prepare files config for generation
        $files = [
            'model' => $this->modelFile,
            'migration' => $this->migrationFile,
            'softDelete' => $this->softDeleteFile,
            'adminCRUDFile' => $this->crudFile,
            'service' => $this->serviceFile,
            'notification' => $this->notificationFile,
            'resource' => $this->resourceFile,
            'request' => $this->requestFile,
            'traits' => $this->traitFiles,
            'observer' => $this->observerFile,
            'policy' => $this->policyFile,
            'factory' => $this->factoryFile,
        ];

        // Format field and relation strings
        $fieldString = collect($this->fieldsData)->pluck('column_name')->implode(', ');
        $relationsString = implode(', ', array_map(
            fn($relation) => ($relation['related_model'] ?? 'unknown') . ':' . ($relationMap[$relation['relation_type']] ?? 'unknown'),
            $this->relationData
        ));

        // Generate files based on flags
        if ($files['model']) {
            $this->generateModel($modelName, $fieldString, $relationsString, $selectedMethods, $files['softDelete'], $files['factory'], $selectedTraits, $this->overwriteFiles);
        }

        if ($files['migration']) {
            $this->generateMigration($modelName, $this->fieldsData, $files['softDelete'], $this->overwriteFiles);
        }

        $this->generateController($modelName, $selectedMethods, $files['service'], $files['resource'], $files['request'], $this->overwriteFiles, $files['adminCRUDFile']);

        if ($files['policy']) {
            $this->generatePolicy($modelName, $this->overwriteFiles);
        }

        if ($files['observer']) {
            $this->generateObserver($modelName, $this->overwriteFiles);
        }

        if ($files['service']) {
            $this->generateService($modelName, $this->overwriteFiles);
        }

        if ($files['notification']) {
            $this->generateNotification($modelName, $this->overwriteFiles);
        }

        if ($files['resource']) {
            $this->generateResource($modelName, $this->overwriteFiles);
        }

        if ($files['request']) {
            $this->generateRequest($modelName, $this->fieldsData, $this->overwriteFiles);
        }

        if ($files['factory']) {
            $this->generateFactory($modelName, $this->fieldsData, $this->overwriteFiles);
        }

        if ($selectedTraits) {
            $this->copyTraits($selectedTraits);
        }
    }

    /**
     * HELPER METHODS FOR FILE GENERATION
     */

    /**
     * Generate model file
     */
    private function generateModel($modelName, $fieldString, $relations, $selectedMethods, $softDelete, $factory, $selectedTraits, $overwrite)
    {
        Artisan::call('codegenerator:model', [
            'modelName' => $modelName,
            '--fields' => $fieldString,
            '--relations' => $relations,
            '--methods' => implode(',', $selectedMethods),
            '--softDelete' => $softDelete,
            '--factory' => $factory,
            '--traits' => implode(',', $selectedTraits),
            '--overwrite' => $overwrite
        ]);
    }

    /**
     * Generate migration file
     */
    private function generateMigration($modelName, $fields, $softDelete, $overwrite)
    {
        $migrationFieldString = collect($fields)->map(function ($field) {
            return $field['column_name'] . ':' . $field['data_type'];
        })->implode(',');

        Artisan::call('codegenerator:migration', [
            'modelName' => $modelName,
            '--fields' => $migrationFieldString,
            '--softdelete' => $softDelete,
            '--overwrite' => $overwrite
        ]);
    }

    /**
     * Generate controller file
     */
    private function generateController($modelName, $selectedMethods, $service, $resource, $request, $overwrite, $adminCrud)
    {
        Artisan::call('codegenerator:controller', [
            'modelName' => $modelName,
            '--methods' => implode(',', $selectedMethods),
            '--service' => $service,
            '--resource' => $resource,
            '--request' => $request,
            '--overwrite' => $overwrite,
            '--adminCrud' => $adminCrud,
        ]);
    }

    /**
     * Generate policy file
     */
    private function generatePolicy($modelName, $overwrite)
    {
        Artisan::call('codegenerator:policy', [
            'modelName' => $modelName,
            '--overwrite' => $overwrite
        ]);
    }

    /**
     * Generate observer file
     */
    private function generateObserver($modelName, $overwrite)
    {
        Artisan::call('codegenerator:observer', [
            'modelName' => $modelName,
            '--overwrite' => $overwrite
        ]);
    }

    /**
     * Generate service file
     */
    private function generateService($modelName, $overwrite)
    {
        Artisan::call('codegenerator:service', [
            'modelName' => $modelName,
            '--overwrite' => $overwrite
        ]);
    }

    /**
     * Generate notification file
     */
    private function generateNotification($modelName, $overwrite)
    {
        $notificationData = !empty($this->notificationData) ? $this->notificationData[0] : [];

        Artisan::call('codegenerator:notification', [
            'className' => $notificationData['class_name'] ?? $modelName . 'Notification',
            '--modelName' => $modelName,
            '--data' => $notificationData['data'] ?? '',
            '--body' => $notificationData['body'] ?? '',
            '--subject' => $notificationData['subject'] ?? '',
            '--overwrite' => $overwrite
        ]);
    }

    /**
     * Generate resource file
     */
    private function generateResource($modelName, $overwrite)
    {
        Artisan::call('codegenerator:resource', [
            'modelName' => $modelName,
            '--overwrite' => $overwrite
        ]);
    }

    /**
     * Generate request file
     */
    private function generateRequest($modelName, $fields, $overwrite)
    {
        $ruleString = implode(',', array_map(function ($field) {
            return $field['column_name'] . ':' . $field['column_validation'];
        }, $fields));

        Artisan::call('codegenerator:request', [
            'modelName' => $modelName,
            '--rules' => $ruleString,
            '--overwrite' => $overwrite
        ]);
    }

    /**
     * Generate factory file
     */
    private function generateFactory($modelName, $fields, $overwrite)
    {
        $fieldString = implode(',', array_map(function ($field) {
            return $field['column_name'] . ':' . $field['data_type'];
        }, $fields));

        Artisan::call('codegenerator:factory', [
            'modelName' => $modelName,
            '--fields' => $fieldString,
            '--overwrite' => $overwrite
        ]);
    }

    /**
     * Copy traits to application
     */
    private function copyTraits($selectedTraits)
    {
        $source = __DIR__ . '/../../TraitsLibrary/Traits';
        $destination = app_path(config('code_generator.trait_path', 'Traits'));

        if (!File::exists($source)) {
            Log::warning('Traits source folder not found: ' . $source);
            return;
        }

        File::ensureDirectoryExists($destination);

        foreach ($selectedTraits as $trait) {
            $fileName = $trait . '.php';
            $sourceFile = $source . DIRECTORY_SEPARATOR . $fileName;
            $destinationFile = $destination . DIRECTORY_SEPARATOR . $fileName;

            if (!File::exists($sourceFile)) {
                Log::warning("Trait file not found in source: $fileName");
                continue;
            }

            if (File::exists($destinationFile)) {
                Log::info("Trait $fileName already exists in destination, skipping.");
                continue;
            }

            File::copy($sourceFile, $destinationFile);
            Log::info("Trait $fileName copied to app/Traits.");
        }
    }


    public function loadMigrationTableNames()
    {
        $migrationPath = database_path('migrations');
        $files = File::exists($migrationPath) ? File::files($migrationPath) : [];

        $this->tableNames = collect($files)->map(function ($file) {
            if (preg_match('/create_(.*?)_table/', $file->getFilename(), $matches)) {
                return $matches[1];
            }
            return null;
        })->filter()->unique()->values()->toArray();
    }

    public function updatedForeignModelName($value)
    {
        if ($value && Schema::hasTable($value)) {
            $this->fieldNames = Schema::getColumnListing($value);
        } else {
            $this->fieldNames = [];
        }
    }
}
