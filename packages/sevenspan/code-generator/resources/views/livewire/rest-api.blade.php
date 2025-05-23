<div class="w-full p-6" x-data="{ 
    showToast: false,
    toastMessage: '',
    toastType: 'success',
    init() {
        Livewire.on('show-toast', (data) => {
            this.toastMessage = data.message;
            this.toastType = data.type;
            this.showToast = false; // Reset first
            setTimeout(() => {
                this.showToast = true;
                setTimeout(() => this.showToast = false, 3000);
            }, 50);
        });
    }
}">

    <!-- Toast Message -->
    <div x-show="showToast" x-transion:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2"
        :class="toastType === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'"
        class="fixed top-4 right-4 border px-4 py-3 rounded z-50 shadow-lg" role="alert">
        <div class="flex items-center">
            <svg x-show="toastType === 'success'" class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd" />
            </svg>
            <svg x-show="toastType === 'error'" class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                    clip-rule="evenodd" />
            </svg>
            <span class="block sm:inline" x-text="toastMessage"></span>
        </div>
    </div>

    <!-- Session Messages -->
    @foreach (['success' => 'green', 'error' => 'red'] as $type => $color)
    @if (session()->has($type))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2"
        class="fixed top-4 right-4 bg-{{ $color }}-100 border border-{{ $color }}-400 text-{{ $color }}-700 px-4 py-3 rounded z-50 shadow-lg"
        role="alert">
        <div class="flex items-center">
            @if ($type === 'success')
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd" />
            </svg>
            @else
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                    clip-rule="evenodd" />
            </svg>
            @endif
            <span class="block sm:inline">{{ session($type) }}</span>
        </div>
    </div>
    @endif
    @endforeach

    <p class="text-red-500 mb-4">Note: To use this CRUD generator you first need to install <a href=""
            class="underline">spatie</a> package, as we are using it in our BaseModel.php file.</p>
    <!-- model input -->
    <div class="pb-4" id="modelNameSection">
        <div>
            <h2 class="grey-900 text-xl font-semibold pb-2">Model Name</h2>
            <input type="text" class="border border-gray-300 rounded-lg px-4 py-2 w-full" placeholder="Enter Name"
                wire:model.live="modelName" />
            <span class="text-s text-gray-600 italic">
                Note: Enter your model name like, Project OR Project Category.
            </span>
            <div>
                @error('modelName')
                <div class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50 shadow-lg"
                    role="alert">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="block sm:inline">{{ $message }}</span>
                    </div>
                </div>
                @enderror
            </div>
        </div>
    </div>

    <!-- fields  -->
    <div class="border border-grey-200 rounded-xl p-6 my-4 bg-white">
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-xl font-semibold">Fields </h2>
            <x-code-generator::button title="Add" @click="$wire.isAddFieldModalOpen=true; $wire.resetForm()" />
        </div>
        <x-code-generator::field-table :$fieldsData  :softDeleteFile="$softDeleteFile"/>
    </div>

    <!-- eloqunet relations -->
    <div class="border border-grey-200 rounded-xl p-6 my-4 bg-white">
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-xl font-semibold">Eloquent Relations</h2>
            <x-code-generator::button title="Add" @click="$wire.isAddRelModalOpen=true; $wire.resetForm()" />
        </div>
        <x-code-generator::relation-table :$relationData />
    </div>

    <!-- checkboxes -->   
    <div class="mb-2">
        <x-code-generator::add-files-methods :$errorMessage />
    </div>

    <!-- Generate files button -->
    <div>
        <x-code-generator::button title="Generate Files" wire:click="save" wire:loading.attr="disabled"
            loadingTarget="save" />
    </div>

    <!-- Modals -->
    <x-code-generator::add-relation-modal />
    <x-code-generator::add-new-field-modal />
    <x-code-generator::edit-relation-modal />
    <x-code-generator::delete-relation-modal />
    <x-code-generator::delete-field-modal />
    <x-code-generator::edit-field-modal />
    <x-code-generator::notification-modal />