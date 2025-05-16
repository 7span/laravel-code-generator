<div>
    <p class="text-red-500 mb-4">Note: To use this CRUD generator you first need to install <a href=""
            class="underline">spatie</a> package, as we are using it in our BaseModel.php file.</p>
    <!-- model input -->
    <div class="pb-4">
        <div>
            <h2 class="grey-900 text-xl font-semibold pb-2">Model Name</h2>
            <input type="text"
            class="border border-gray-300 rounded-lg px-4 py-2 w-full"
            placeholder="Enter Name" 
            wire:model.live="modelName" />
            <span class="text-s text-gray-600 italic">
                Note: Enter your model name like, Project OR Project Category.
            </span>
            <div>
            @error('modelName')
            <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
            </div>
        </div>
    </div>
    <!-- eloqunet relation -->
    <div class="border border-grey-200 rounded-xl p-6 my-4 bg-white">
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-xl font-semibold">Add Eloquent Relationship</h2>
            <x-code-generator::button title="Add" 
                @click="$wire.isAddRelModalOpen=true; $wire.resetForm()" />
        </div>
        <x-code-generator::eloqunet-relation-table :$relationData />
    </div>
    <!-- new fields input -->
    <div class="border border-grey-200 rounded-xl p-6 my-4 bg-white">
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-xl font-semibold">Add New Fields </h2>
            <x-code-generator::button title="Add"
                @click="$wire.isAddFieldModalOpen=true; $wire.resetForm()" />
        </div>

        <x-code-generator::field-table :$fieldsData />
    </div>
        <x-code-generator::add-files-methods :$errorMessage />
     @if ($generalError)
        <p class="text-red-500 mt-2">{{ $generalError }}</p>
    @endif
    <div>
        <x-code-generator::button title="Generate REST API Files" wire:click="save" />
    </div>
    <x-code-generator::add-relation-modal />
    <x-code-generator::add-new-field-modal />
    <x-code-generator::edit-relation-modal />
    <x-code-generator::delete-relation-modal />
    <x-code-generator::delete-field-modal />
    <x-code-generator::edit-field-modal />
    <x-code-generator::notification-modal />
<div>