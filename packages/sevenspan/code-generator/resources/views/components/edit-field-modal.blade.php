<div wire:show="isEditFieldModalOpen" x-data x-transition.duration.200ms
    x-on:click.self="$wire.isEditFieldModalOpen=false"
    class="fixed top-0 left-0 flex items-center justify-center w-full h-full bg-gray-500 bg-opacity-50 z-50">

    <x-code-generator::modal modalTitle="Edit Field">
        <!-- Modal header -->
        <x-slot:closebtn>
            <button x-on:click="$wire.isEditFieldModalOpen=false"
                class="text-gray-500 hover:text-black text-xl">&times;</button>
        </x-slot:closebtn>
        <div class="mt-4 space-y-4">
            <!-- Data Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data Type</label>
                <select id="column_type" class="w-full border rounded-md p-2" name="data_type" wire:model="data_type">
                    <x-code-generator::data-type-option />
                </select>
                @error('data_type') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Column Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Column Name</label>
                <input type="text" placeholder="Enter Name " wire:model="column_name"
                    class="w-full border rounded-md p-2 placeholder:text-gray-400 placeholder:text-[16px]" />
                @error('column_name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                <p class="text-xs italic text-gray-500 mt-1">Note: Add without special characters</p>
            </div>

            <!-- Validation -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Validation</label>
                <select wire:model="column_validation" id="column_validation"
                    class="form-control w-full border rounded-md p-2" name="column_validation">
                    <option value="optional">Optional</option>
                    <option value="required">Required</option>
                    <option value="unique">Unique</option>
                    <option value="email">Email</option>
                </select>
                @error('column_validation') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Add Scope -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Add Scope In The Model File</label>
                <div class="flex items-center gap-6">
                    <label class="inline-flex items-center">
                        <input type="radio" wire:model="add_scope" name="option" value="yes"
                            class="form-radio text-orange-500" checked>
                        <span class="ml-2">Yes</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" wire:model="add_scope" name="option" value="no"
                            class="form-radio text-orange-500">
                        <span class="ml-2">No</span>
                    </label>
                </div>
                @error('add_scope') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>
        <!-- Modal footer -->
        <x-slot:footer>
            <div class="mr-6">
            <x-code-generator::button title="Cancel" x-on:click="$wire.isEditFieldModalOpen=false"/>
          </div>
          <x-code-generator::button wire:click="saveField" title="Update" />
        </x-slot:footer>
        </x-modal>
</div>