<?php

namespace Sevenspan\CodeGenerator\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;
use Sevenspan\CodeGenerator\Console\Commands\MakeRequest;

class RestApi extends Component
{
    public array $relationData = [];
    public array $fieldsData = [];
    public array $notificationData = [];
    public $generalError = ''; 

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
    public $traitFiles = false;

    public $errorMessage = "";
    protected $rules = [
        'modelName' => 'required|regex:/^[A-Z][A-Za-z ]+$/',

        'related_model' => 'required|regex:/^[A-Z][A-Za-z ]+$/',
        'relation_type' => 'required',
        'second_model' => 'required|regex:/^[A-Za-z ]+$/',
        'foreign_key' => 'required',
        'local_key' => 'required',

        'data_type' => 'required',
        'column_name' => 'required|regex:/^[A-Za-z ]+$/',
        'column_validation' => 'required',
        'add_scope' => 'required',
        
        'class_name' => 'required|regex:/^[A-Z][A-Za-z ]+$/',
        'data' => 'required|regex:/^\\s*\\[\\s*(["\']?[A-Za-z]+["\']?\\s*=>\\s*\\d+\\s*(?:,\\s*["\']?[A-Za-z]+["\']?\\s*=>\\s*\\d+)*)?\\s*\\]$/',
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

    public function openEditFieldModal($fieldId)
    {
        $this->fieldId = $fieldId;
        $this->isEditFieldModalOpen = true;
        $field = collect($this->fieldsData)->firstWhere('id', $fieldId);
        if ($field) {
            $this->fill($field);
        }
    }

    public function openDeleteFieldModal($id)
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
        if (!($this->index || $this->store || $this->show || $this->destroy || $this->update)) {
            $this->errorMessage = "Please select at least one method.";
        } else {
            $this->errorMessage = "";
        }
    }

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
        ]);
        $this->resetErrorBag();
    }

    public function save(): void
    {
    $formData = $this->validate([
        'modelName' => $this->rules['modelName']
    ]);
    $relations = $this->relationData;
    $fields = $this->fieldsData;
    $notification = $this->notificationData;
    $formData = array_merge(
        $formData,
        [
            'modelFile' => $this->modelFile,
            'migrationFile' => $this->migrationFile,
            'softDeleteFile' => $this->softDeleteFile,
            'crudFile' => $this->crudFile,
            'serviceFile' => $this->serviceFile,
            'notificationFile' => $this->notificationFile,
            'resourceFile' => $this->resourceFile,
            'requestFile' => $this->requestFile,
            'traitFiles' => $this->traitFiles,
            'index' => $this->index,
            'store' => $this->store,
            'show' => $this->show,
            'update' => $this->update,
            'destroy' => $this->destroy,
            'relations' => $relations,
            'fields' => $fields,
            'notification' => $notification,
        ]
    );
    if (!empty($fields) && ($this->index || $this->store || $this->show || $this->update || $this->destroy)) {
                //dd($formData);
        // Use Livewire's server-side call to a controller action
        $response = app(MakeRequest::class)->processFormData(new \Illuminate\Http\Request($formData));
        // Response should contain JSON from controller method
        if ($response->getStatusCode() === 200) {  
            // Handle response (e.g., display success message)
            $responseData = json_decode($response->getContent(), true);
            session()->flash('success', $responseData['message']);
            // You might also want to clear form fields or redirect
            $this->resetForm();
        } else {
            // Handle errors (e.g., display error message)
            session()->flash('error', 'An error occurred while processing the data.'); 
        } 
    } else {
        $this->generalError = 'Please add at least one field and select at least one method.';
    }
}
    public function render()
    {
        return view('code-generator::livewire.rest-api');
    }
}