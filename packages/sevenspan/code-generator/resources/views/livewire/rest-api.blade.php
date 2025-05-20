<div class="w-full p-6">
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
            <h2 class="text-xl font-semibold">Eloquent Relations</h2>
            <x-code-generator::button title="Add" 
                @click="$wire.isAddRelModalOpen=true; $wire.resetForm()" />
        </div>
        <x-code-generator::eloqunet-relation-table :$relationData />
    </div>
    <!-- new fields input -->
    <div class="border border-grey-200 rounded-xl p-6 my-4 bg-white">
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-xl font-semibold">Fields </h2>
            <x-code-generator::button title="Add"
                @click="$wire.isAddFieldModalOpen=true; $wire.resetForm()" />
        </div>

        <x-code-generator::field-table :$fieldsData />
    </div>
        <x-code-generator::add-files-methods :$errorMessage />
 <!-- messsages -->
     @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @elseif ($generalError)
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ $generalError }}</span>
        </div>
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