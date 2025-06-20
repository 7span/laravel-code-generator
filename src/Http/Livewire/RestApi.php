<?php

namespace Sevenspan\CodeGenerator\Http\Livewire;


use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Sevenspan\CodeGenerator\Library\Helper;

class RestApi extends Component
{
    // Data properties
    public array $relationTypes = [];
    public array $relationData = [];
    public array $fieldsData = [];
    public array $notificationData = [];
    public array $tableNames = [];
    public array $modelNames = [];
    public $fieldNames = [];
    public $columnNames = [];
    public $baseFields = [];
    public $intermediateFields = [];
    public $defaultFields = [];


    public $generalError = '';
    public $errorMessage = "";
    public $successMessage = '';
    public $query = '';

    // Foreign key properties
    public $is_foreign_key = false;
    public $foreign_model_name = '';
    public $referenced_column = '';
    public $on_delete_action = '';
    public $on_update_action = '';

    // Modal visibility properties
    public $isAddRelModalOpen = false;
    public $isRelDeleteModalOpen = false;
    public $isAddFieldModalOpen = false;
    public $isDeleteFieldModalOpen = false;
    public $isNotificationModalOpen = false;
    public $isResetFormModalOpen = false;
    
    // Form inputs
    public $model_name;

    public $relations, $relationId, $fields, $fieldId;

    // Relationship form fields
    public $related_model, $relation_type, $intermediate_model, $foreign_key, $local_key, $intermediate_foreign_key, $intermediate_local_key;

    // Field properties
    public $data_type, $column_name, $column_validation;

    // Notification properties
    public $class_name, $data, $subject, $body;

    // Method checkboxes
    public $is_index_method_added = true;
    public $is_store_method_added = true;
    public $is_show_method_added = true;
    public $is_update_method_added = true;
    public $is_destroy_method_added = true;

    // File generation options
    public $is_model_file_added = false;
    public $is_migration_file_added = false;
    public $is_soft_delete_added = false;
    public $is_admin_crud_added = false;
    public $is_service_file_added = false;
    public $is_notification_file_added = false;
    public $is_resource_file_added = false;
    public $is_request_file_added = false;
    public $is_trait_files_added = false;
    public $is_overwrite_files = false;
    public $is_observer_file_added = false;
    public $is_factory_file_added = false;
    public $is_policy_file_added = false;
    public $is_select_all_files_checked = false;
    public $is_select_all_methods_checked = true;
    public $is_select_all_traits_checked = false;

    // Trait checkboxes
    public $is_boot_model_trait_added = false;
    public $is_pagination_trait_added = false;
    public $is_resource_filterable_trait_added = false;
    public $is_has_uuid_trait_added = false;
    public $is_has_user_action_trait_added = false;
    public $isEditing = false;

    // Validation rules
    protected $rules = [
        'model_name' => [
            'required',
            'regex:/^[A-Z][A-Za-z]+$/',
            'not_in:Default,Model,Controller,Request,Route,Middleware,View,New,Return,Class,Function',
        ],
        'related_model' => 'required|regex:/^[A-Z][A-Za-z]+$/',
        'relation_type' => 'required',
        'intermediate_model' => 'required|different:model_name|different:related_model|regex:/^[A-Z][A-Za-z]+$/',
        'foreign_key' => 'required|string|regex:/^[a-z]+(_[a-z]+)*$/',
        'local_key' => 'required|string|regex:/^[a-z]+(_[a-z]+)*$/',
        'intermediate_foreign_key' => 'required|string|regex:/^[a-z]+(_[a-z]+)*$/',
        'intermediate_local_key' => 'required|string|regex:/^[a-z]+(_[a-z]+)*$/',
        'data_type' => 'required',
        'column_name' => 'required|regex:/^[a-z_]+$/',
        'column_validation' => 'required',
        'class_name' => 'required|regex:/^[A-Z][A-Za-z]+$/',
        'data' => 'required|regex:/^[A-Za-z0-9_]+:[A-Za-z0-9_]+(?:,[A-Za-z0-9_]+:[A-Za-z0-9_]+)*$/',
        'subject' => 'required|regex:/^[A-Za-z_ ]+$/',
        'body' => 'required|regex:/^[A-Za-z_ ]+$/',
        'foreign_model_name' => 'required|regex:/^[A-Za-z][a-z0-9_]*$/',
        'on_delete_action' => 'nullable|in:restrict,cascade,set null,no action',
        'on_update_action' => 'nullable|in:restrict,cascade,set null,no action',
    ];

    // Custom validation messages
    public $messages = [
        'model_name.regex' => 'The Model Name must start with an uppercase letter and contain only letters.',
        'related_model.regex' => 'The Model Name must start with an uppercase letter and contain only letters.',
        'model_name.max' => 'The Model Name must not exceed 255 characters.',
        'related_model.max' => 'The Model Name must not exceed 255 characters.',
        'model_name.not_in' => 'The Model Name cannot be a reserved word.',
    ];

    // Initialize component
    public function render()
    {
        return view('code-generator::livewire.rest-api');
    }

    // Mount component
    public function mount()
    {
         $this->defaultFields = $this->getDefaultFields();
         $this->updatedIsSoftDeleteAdded();
    }

     protected function getDefaultFields(): array
    {
        return [
            ['column_name' => 'id', 'data_type' => 'auto_increment', 'column_validation' => 'required'],
            ['column_name' => 'created_by', 'data_type' => 'integer', 'column_validation' => 'nullable'],
            ['column_name' => 'updated_by', 'data_type' => 'integer', 'column_validation' => 'nullable'],
            ['column_name' => 'created_at', 'data_type' => 'datetime', 'column_validation' => 'required'],
            ['column_name' => 'updated_at', 'data_type' => 'datetime', 'column_validation' => 'nullable'],
        ];
    }

    // Update soft delete fields
    public function updatedIsSoftDeleteAdded()
    {
        $softDeleteFields = [
            [
                'id' => 'deleted_at', 
                'column_name' => 'deleted_at',
                'data_type' => 'datetime',
                'column_validation' => 'nullable',
            ],
            [
                'id' => 'deleted_by', 
                'column_name' => 'deleted_by',
                'data_type' => 'integer',
                'column_validation' => 'nullable',
            ],
        ];

        $this->fieldsData = collect($this->fieldsData)
            ->reject(function ($field) {
                 return in_array($field['column_name'], ['deleted_by', 'deleted_at']);
        })
        ->when($this->is_soft_delete_added, function ($collection) use ($softDeleteFields) {
            return $collection->concat($softDeleteFields);
        })
        ->values() 
        ->toArray();
    }

    // select all files checkbox state
    public function updatedIsSelectAllFilesChecked($value)
    {
        $files = [
            'is_migration_file_added',
            'is_admin_crud_added',
            'is_policy_file_added',
            'is_observer_file_added',
            'is_service_file_added',
            'is_notification_file_added',
            'is_resource_file_added',
            'is_request_file_added',
            'is_factory_file_added',
            'is_model_file_added',
        ];

        foreach ($files as $file) {
            $this->$file = $value;
        }
    }

   // select all methods checkbox state
    public function updatedIsSelectAllMethodsChecked($value)
    {
         $methods = [
            'is_store_method_added',
            'is_show_method_added',
            'is_update_method_added',
            'is_destroy_method_added',
            'is_index_method_added',
        ];

        foreach ($methods as $method) {
            $this->$method = $value;
        }
    }

    // select all traits checkbox state
     public function updatedIsSelectAllTraitsChecked($value)
    {
        $traits = [
            'is_boot_model_trait_added',
            'is_pagination_trait_added',
            'is_resource_filterable_trait_added',
            'is_has_uuid_trait_added',
            'is_has_user_action_trait_added',
        ];

        foreach ($traits as $trait) {
            $this->$trait = $value;
        }
    }

    // Add updated method for foreign key checkbox
    public function updatedIsForeignKey($value)
    {
        if ($value) {
            $this->tableNames = Helper::getTableNamesFromMigrations();
        } else {
            $this->foreign_model_name = '';
            $this->referenced_column = '';
            $this->on_delete_action = '';
            $this->on_update_action = '';
            $this->tableNames = [];
        }
    }

    // Prefill query from the create table statement
    public function prefillQuery()
    {
        $result = Helper::parseCreateTable($this->query);
        $this->model_name = $result['model_name'];

        $duplicateColumns = [];
        $defaultColumns = array_column($this->getDefaultFields(), 'column_name');

        // Validate if model_name and fields are present in the query
        if (empty($result['model_name']) || empty($result['fields'])) {
            $this->addError('prefill', 'Invalid CREATE TABLE query.');
            $this->successMessage = null;
            return;
        }

        // Add model name if it exists
        if (!empty($result['model_name'])) {
            $this->model_name = $result['model_name'];
        }

        $newFieldsAdded = false;

        // Merge existing $fieldsData with new ones without duplicates
        foreach ($result['fields'] as $newField) {
             $alreadyExists = collect($this->fieldsData)->contains('column_name', $newField['column_name'])
                   || in_array($newField['column_name'], $defaultColumns);
                if ($alreadyExists) {
                    $duplicateColumns[] = $newField['column_name'];
                    continue;
                }
            $this->fieldsData[] = $newField;
            $newFieldsAdded = true;
        }

        if (!empty($duplicateColumns)) {
           $this->addError('prefill', 'Skipped  following columns as they already exist: ' . implode(', ', $duplicateColumns).'.');
        }
         if ($newFieldsAdded) {
            session()->flash('success', 'Model name and fields added successfully!');
        } 
    }

    // Live validation for form fields
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }


    // collect the model names when the modal is opened
    public function updatedIsAddRelModalOpen($value)
    {
        if ($value) {
            $this->relationTypes = Helper::getRelationTypes();
            $this->modelNames = collect(Helper::getTableNamesFromMigrations())
                ->map(function ($name) {
                    return Str::studly(Str::singular($name));
                })->toArray();
        }
    }

    // Update notification file checkbox state and open modal if checked
    public function updatedIsNotificationFileAdded($value): void
    {
        if ($value) {
            $this->isNotificationModalOpen = true;
        }
    }

    public function validateFieldsAndMethods()
    {
        $this->errorMessage = "";

        // Check if any file that requires fields is selected
        $requiresFields = $this->is_model_file_added || $this->is_migration_file_added || $this->is_request_file_added || $this->is_factory_file_added;

        // If fields are required but none are added
        if ($requiresFields && empty($this->fieldsData)) {
            $this->errorMessage = "Please add at least one field for the selected file types.";
            return false;
        }

        // Check for methods
        if (!($this->is_index_method_added || $this->is_store_method_added || $this->is_show_method_added || $this->is_destroy_method_added || $this->is_update_method_added)) {
            $this->errorMessage = "Please select at least one method.";
            return false;
        }

        return true;
    }

    // Open delete modal
    public function openDeleteModal($id): void
    {
        $this->relationId = $id;
        $this->isRelDeleteModalOpen = true;
    }

    // Delete relation in table
    public function deleteRelation(): void
    {
        $this->relationData = array_filter($this->relationData, function ($relation) {
            return $relation['id'] !== $this->relationId;
        });
        $this->isRelDeleteModalOpen = false;
    }

    // Open edit relation modal
    public function openEditRelationModal($relationId): void
    {
        $this->relationId = $relationId;
        $this->isEditing = true;
        $this->isAddRelModalOpen = true;
        $relation = collect($this->relationData)->firstWhere('id', $relationId);
        if ($relation) {
            $this->fill($relation);
        }
    }

    // Reset form fields and error messages
    public function resetForm()
    {
        $this->reset();
        $this->resetErrorBag();
        $this->sessionMessage = '';
        $this->mount();
    }

    // Resets modal form fields
    public function resetModal()
    {
        $this->reset([
            'related_model',
            'relation_type',
            'intermediate_model',
            'foreign_key',
            'local_key',
            'data_type',
            'column_name',
            'column_validation',
            'fieldId',
            'is_foreign_key',
            'foreign_model_name',
            'referenced_column',
            'intermediate_foreign_key',
            'intermediate_local_key',
            'on_delete_action',
            'on_update_action',
        ]);
        $this->resetErrorBag();
    }

    // Save relation data
    public function saveRelation(): void
    {
        $rules = [
            'related_model' => $this->rules['related_model'],
            'relation_type' => $this->rules['relation_type'],
            'foreign_key' => $this->rules['foreign_key'],
            'local_key' => $this->rules['local_key'],
        ];

        $isThroughRelation = in_array($this->relation_type, ['hasOneThrough', 'hasManyThrough']);

        // Add intermediate model rules only for through relations
        if ($isThroughRelation) {
            $rules['intermediate_model'] = $this->rules['intermediate_model'];
            $rules['intermediate_foreign_key'] = $this->rules['intermediate_foreign_key'];
            $rules['intermediate_local_key'] = $this->rules['intermediate_local_key'];
        }

        $this->validate($rules);

        if (
            $this->foreign_key === $this->local_key &&
            $this->related_model === $this->model_name
        ) {
            $this->addError('local_key', 'Foreign key and local key cannot be the same as base model for self-relation.');
            return;
        }

        //// Custom Logic Validation for "Through" Relationships
        if ($isThroughRelation) {
            if ($this->foreign_key === $this->intermediate_foreign_key) {
                $this->addError('intermediate_foreign_key', 'The "Foreign Key on Intermediate Model" must be different from the "Foreign Key on Related Model"');
            }
        }

        $relationData = [
            'related_model' => $this->related_model,
            'relation_type' => $this->relation_type,
            'foreign_key' => $this->foreign_key,
            'local_key' => $this->local_key,
            'intermediate_model' => $isThroughRelation ? $this->intermediate_model : '',
            'intermediate_foreign_key' => $isThroughRelation ? $this->intermediate_foreign_key : '',
            'intermediate_local_key' => $isThroughRelation ? $this->intermediate_local_key : '',
        ];

        // Check for duplicates
        foreach ($this->relationData as $existing) {
            if (
                $existing['related_model'] === $this->related_model &&
                $existing['relation_type'] === $this->relation_type &&
                $existing['foreign_key'] === $this->foreign_key &&
                $existing['local_key'] === $this->local_key &&
                (!isset($existing['intermediate_model']) || $existing['intermediate_model'] === $this->intermediate_model)
            ) {
                $this->addError('related_model', 'This exact relation already exists.');
                return;
            }
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
        $this->reset(['related_model', 'relation_type', 'intermediate_model', 'foreign_key', 'local_key', 'intermediate_foreign_key', 'intermediate_local_key']);
        $this->relationId = null;
    }

    // Open Edit Field Modal
    public function openEditFieldModal($fieldId): void
    {
        $this->fieldId = $fieldId;
        $this->isEditing = true;
        $this->isAddFieldModalOpen = true;
        $field = collect($this->fieldsData)->firstWhere('id', $fieldId);
        if ($field) {
            $this->fill($field);
        }
    }

    // Opens delete  Field Modal
    public function openDeleteFieldModal($id): void
    {
        $this->fieldId = $id;
        $this->isDeleteFieldModalOpen = true;
    }

    // Deletes field from table
    public function deleteField(): void
    {
        $this->fieldsData = array_filter($this->fieldsData, function ($field) {
            return $field['id'] !== $this->fieldId;
        });
        $this->isDeleteFieldModalOpen = false;
    }

    protected function isDuplicateColumn(): bool
    {
        foreach ($this->fieldsData as $field) {
            if (
                $field['column_name'] === $this->column_name &&
                (!$this->fieldId || $field['id'] !== $this->fieldId)
            ) {
                return true;
            }
        }
        return false;
    }

    // Save Fields Data
    public function saveField(): void
    {
        // Check for duplicate column name, excluding the current edited field by ID
        if ($this->isDuplicateColumn()) {
            $this->addError('column_name', 'You have already taken this column');
            return;
        }

        $rulesToValidate = [
            'data_type' => $this->rules['data_type'],
            'column_name' => $this->rules['column_name'],
            'column_validation' => $this->rules['column_validation'],
        ];

        if ($this->is_foreign_key) {
            $rulesToValidate['foreign_model_name'] = $this->rules['foreign_model_name'];
            $rulesToValidate['referenced_column'] = $this->rules['local_key'];
            $rulesToValidate['on_delete_action'] = $this->rules['on_delete_action'];
            $rulesToValidate['on_update_action'] = $this->rules['on_update_action'];
        }

        $this->validate($rulesToValidate);
        
        $fieldData = [
            'data_type' => $this->data_type,
            'column_name' => $this->column_name,
            'column_validation' => $this->column_validation,
            'is_foreign_key' => $this->is_foreign_key ?? false,
            'foreign_model_name' => $this->foreign_model_name,
            'referenced_column' => $this->referenced_column,
            'on_delete_action' => $this->on_delete_action,
            'on_update_action' => $this->on_update_action,
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
        $this->isAddFieldModalOpen = false;
        $this->fieldId = null;
        $this->reset(['column_name', 'data_type', 'column_validation', 'is_foreign_key', 'foreign_model_name', 'referenced_column', 'on_delete_action', 'on_update_action']);
    }

    // Save notification data
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

    //Validate inputs before generation 
    private function validateInputs(): bool
    {
        // Validate model name
        $this->validate(['model_name' => $this->rules['model_name']]);

        // Check if model exists and overwrite is not checked
        $modelPath = app_path('Models/' . $this->model_name . '.php');
        if (File::exists($modelPath) && !$this->is_overwrite_files) {
            $this->errorMessage = "Model {$this->model_name} already exists if you want to overwrite it check the 'Overwrite Files' option";
            session()->flash('error', $this->errorMessage);
            $this->dispatch('show-toast', ['message' => $this->errorMessage, 'type' => 'error']);
            return false;
        }

        // Check if notification file is selected but no notification data is provided
        if ($this->is_notification_file_added && empty($this->notificationData)) {
            $this->errorMessage = "Please add notification data before generating files.";
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

    // Save Form and generate files
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
            $this->mount();
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            session()->flash('error', $e->getMessage());
        }
    }

    protected function getSelectedTraits(): array
    {
        return array_filter([
            'ApiResponser',
            'BaseModel',
            $this->is_boot_model_trait_added ? 'BootModel' : null,
            $this->is_pagination_trait_added ? 'PaginationTrait' : null,
            $this->is_resource_filterable_trait_added ? 'ResourceFilterable' : null,
            $this->is_has_uuid_trait_added ? 'HasUuid' : null,
            $this->is_has_user_action_trait_added ? 'HasUserAction' : null,
        ]);
    }
    // Generate all selected files
    private function generateFiles(): void
    {
        $selectedTraits = $this->getSelectedTraits();

        // Prepare selected methods
        $selectedMethods = array_filter([
            $this->is_index_method_added ? 'index' : null,
            $this->is_store_method_added ? 'store' : null,
            $this->is_show_method_added ? 'show' : null,
            $this->is_update_method_added ? 'update' : null,
            $this->is_destroy_method_added ? 'destroy' : null,
        ]);

        // Format field and relation strings
        $fieldString = collect($this->fieldsData)->pluck('column_name')->implode(', ');

        // Generate files based on flags
        if ($this->is_model_file_added) {
            $this->generateModel($this->model_name, $fieldString, $this->relationData, $selectedMethods,  $this->is_soft_delete_added, $this->is_factory_file_added, $selectedTraits, $this->is_overwrite_files);
        }

        if ($this->is_migration_file_added) {
            $this->generateMigration($this->model_name, $this->fieldsData, $this->is_soft_delete_added, $this->is_overwrite_files);
        }

        $this->generateController($this->model_name, $selectedMethods, $this->is_service_file_added, $this->is_resource_file_added, $this->is_request_file_added, $this->is_overwrite_files, $this->is_admin_crud_added);

        if ($this->is_policy_file_added) {
            $this->generatePolicy($this->model_name, $this->is_overwrite_files);
        }

        if ($this->is_observer_file_added) {
            $this->generateObserver($this->model_name, $this->is_overwrite_files);
        }

        if ($this->is_service_file_added) {
            $this->generateService($this->model_name, $this->is_overwrite_files);
        }

        if ($this->is_notification_file_added) {
            $this->generateNotification($this->model_name, $this->is_overwrite_files);
        }

        if ($this->is_resource_file_added) {
            $this->generateResource($this->model_name, $this->is_overwrite_files);
        }

        if ($this->is_request_file_added) {
            $this->generateRequest($this->model_name, $this->fieldsData, $this->is_overwrite_files);
        }

        if ($this->is_factory_file_added) {
            $this->generateFactory($this->model_name, $this->fieldsData, $this->is_overwrite_files);
        }

        if ($selectedTraits) {
            $this->copyTraits($selectedTraits);
        }
    }

    /**
     * HELPER METHODS FOR FILE GENERATION
     */

    // Generate model file
    private function generateModel($modelName, $fieldString, $relations, $selectedMethods, $softDelete, $factory, $selectedTraits, $overwrite)
    {
        Artisan::call('code-generator:model', [
            'model' => $modelName,
            '--fields' => $fieldString,
            '--relations' => $relations,
            '--methods' => implode(',', $selectedMethods),
            '--softDelete' => $softDelete,
            '--factory' => $factory,
            '--traits' => implode(',', $selectedTraits),
            '--overwrite' => $overwrite
        ]);
    }

    //Generate migration file
    private function generateMigration($modelName, $fields, $softDelete, $overwrite)
    {
        Artisan::call('code-generator:migration', [
            'model' => $modelName,
            '--fields' => $fields,
            '--softdelete' => $softDelete,
            '--overwrite' => $overwrite
        ]);
    }

    // Generate controller file
    private function generateController($modelName, $selectedMethods, $service, $resource, $request, $overwrite, $adminCrud)
    {
        Artisan::call('code-generator:controller', [
            'model' => $modelName,
            '--methods' => implode(',', $selectedMethods),
            '--service' => $service,
            '--resource' => $resource,
            '--request' => $request,
            '--overwrite' => $overwrite,
            '--adminCrud' => $adminCrud,
        ]);
    }

    // Generate policy file
    private function generatePolicy($modelName, $overwrite)
    {
        Artisan::call('code-generator:policy', [
            'model' => $modelName,
            '--overwrite' => $overwrite
        ]);
    }

    // Generate observer file
    private function generateObserver($modelName, $overwrite)
    {
        Artisan::call('code-generator:observer', [
            'model' => $modelName,
            '--overwrite' => $overwrite
        ]);
    }

    // Generate service file
    private function generateService($modelName, $overwrite)
    {
        Artisan::call('code-generator:service', [
            'model' => $modelName,
            '--overwrite' => $overwrite
        ]);
    }

    //Generate notification file
    private function generateNotification($modelName, $overwrite)
    {
        $notificationData = !empty($this->notificationData) ? $this->notificationData[0] : [];

        Artisan::call('code-generator:notification', [
            'className' => $notificationData['class_name'] ?? $modelName . 'Notification',
            '--model' => $modelName,
            '--data' => $notificationData['data'] ?? '',
            '--body' => $notificationData['body'] ?? '',
            '--subject' => $notificationData['subject'] ?? '',
            '--overwrite' => $overwrite
        ]);
    }

    // Generate resource file
    private function generateResource($modelName, $overwrite)
    {
        Artisan::call('code-generator:resource', [
            'model' => $modelName,
            '--overwrite' => $overwrite
        ]);
    }

    // Generate request file
    private function generateRequest($modelName, $fields, $overwrite)
    {
        $ruleString = implode(',', array_map(function ($field) {
            return $field['column_name'] . ':' . $field['column_validation'];
        }, $fields));

        Artisan::call('code-generator:request', [
            'model' => $modelName,
            '--rules' => $ruleString,
            '--overwrite' => $overwrite
        ]);
    }

    //Generate factory file
    private function generateFactory($modelName, $fields, $overwrite)
    {
        $fieldString = implode(',', array_map(function ($field) {
            return $field['column_name'] . ':' . $field['data_type'];
        }, $fields));

        Artisan::call('code-generator:factory', [
            'model' => $modelName,
            '--fields' => $fieldString,
            '--overwrite' => $overwrite
        ]);
    }


    private  function copyTraits(array $selectedTraits): void
    {
        $source = __DIR__ . '/../../TraitsLibrary/Traits';
        $destination = base_path(config('code-generator.paths.trait', 'App\Traits'));

        if (!File::exists($source)) {
            return;
        }

        File::ensureDirectoryExists($destination);

        foreach ($selectedTraits as $trait) {
            $fileName = $trait . '.php';
            $sourceFile = $source . DIRECTORY_SEPARATOR . $fileName;
            $destinationFile = $destination . DIRECTORY_SEPARATOR . $fileName;

            // Skip if the source trait file does not exist
            if (!File::exists($sourceFile)) {
                continue;
            }

            // Skip if the destination trait file already exists
            if (File::exists($destinationFile)) {
                continue;
            }

            File::copy($sourceFile, $destinationFile);
        }
    }
    // Update field names based on foreign model name
    public function updatedForeignModelName($value)
    {
        if ($value) {
            $this->fieldNames = [];
            $this->fieldNames = Helper::getColumnNames($value);
            $this->reset(['referenced_column']);
        }
    }

    // loadw field names when related model changes
    public function updatedRelatedModel($value)
    {
        if ($value) {
            $this->columnNames = [];
            $this->columnNames = Helper::getColumnNames('App\\' . config('code-generator.paths.model', 'Models') . '\\' . $value);
            $this->reset('foreign_key');
        }
    }

    // loads intermediate fields when intermediate model changes
    public function updatedIntermediateModel($value)
    {
        if ($value) {
            $this->intermediateFields = []; // Always reset first
            $this->intermediateFields = Helper::getColumnNames('App\\' . config('code-generator.paths.model', 'Models') . '\\' . $value);
            $this->reset('intermediate_foreign_key', 'intermediate_local_key'); // Reset intermediate keys
        }
    }

    public function updatedRelationType($value)
    {
        // If the relation type is not a "through" relation, clear intermediate fields
        if (!in_array($value, ['hasOneThrough', 'hasManyThrough'])) {
            $this->intermediate_model = '';
            $this->intermediate_foreign_key = '';
            $this->intermediate_local_key = '';
            $this->intermediateFields = [];
        }
    }
}
