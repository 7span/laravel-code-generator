<div wire:show="isEditFieldModalOpen" x-data x-transition.duration.200ms
    x-on:click.self="$wire.isEditFieldModalOpen=false"
    class="fixed top-0 left-0 flex items-center justify-center w-full h-full bg-gray-500 bg-opacity-50 z-50">

    <x-code-generator::modal modalTitle="Edit Field">

        <x-slot:closebtn>
            <button x-on:click="$wire.isEditFieldModalOpen=false"
                class="text-gray-500 hover:text-black text-xl">&times;</button>
        </x-slot:closebtn>

        <div class="mt-4 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Column Name</label>
                <input type="text" placeholder="Enter Name" wire:model.live="column_name"
                    class="w-full border rounded-md p-2 placeholder:text-gray-400 placeholder:text-[16px]" />
                @error('column_name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                <p class="text-xs italic text-gray-500 mt-1">Note: Add without special characters</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data Type</label>
                <select id="column_type" class="w-full border rounded-md p-2" name="data_type"
                    wire:model.live="data_type">
                    <x-code-generator::data-type-option />
                </select>
                @error('data_type') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Validation</label>
                <select wire:model.live="column_validation" id="column_validation"
                    class="form-control w-full border rounded-md p-2" name="column_validation">
                    <option value="">Select one</option>
                    <option value="optional">Optional</option>
                    <option value="required">Required</option>
                    <option value="unique">Unique</option>
                    <option value="email">Email</option>
                </select>
                @error('column_validation') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" wire:model.live="isForeignKey" class="form-checkbox text-indigo-600">
                    <span class="text-sm text-gray-800">Make it a foreign key?</span>
                </div>
                @error('isForeignKey') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

        <!-- Show if Foreign Key is selected -->
        @if($this->isForeignKey)

            <div class="mt-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Related Model Name</label>
                @if (!empty($this->tableNames))
                <select wire:model.live="foreignModelName" class="w-full border rounded-md p-2 text-gray-700">
                    <option value="">-- Select Table --</option>
                    @foreach ($this->tableNames as $table)
                    <option value="{{ $table }}">{{ $table }}</option>
                    @endforeach
                </select>
                @else
                <input type="text" placeholder="users" wire:model.live="foreignModelName"
                    class="w-full border rounded-md p-2 placeholder:text-gray-400 placeholder:text-[16px]" />
                <p class="text-xs italic text-gray-500 mt-1">Note: add in plural case e.g., users</p>
                @endif
                @error('foreignModelName') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mt-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Referenced Column</label>
                @if(!empty($this->fieldNames))
                <select wire:model.live="referencedColumn" class="w-full border rounded-md p-2 text-gray-700">
                    <option value="">-- Select field --</option>
                    @foreach ($this->fieldNames as $field)
                    <option value="{{ $field }}">{{ $field }}</option>
                    @endforeach
                </select>
                @else
                <input type="text" placeholder="user_id" wire:model.live="referencedColumn"
                    class="w-full border rounded-md p-2 placeholder:text-gray-400 placeholder:text-[16px]" />
                @endif
                @error('referencedColumn') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            @endif
        </div>

        <x-slot:footer>
            <div class="mr-6">
                <x-code-generator::button title="Cancel" x-on:click="$wire.isEditFieldModalOpen=false" />
            </div>
            <x-code-generator::button wire:click="saveField" title="Update" />
        </x-slot:footer>

    </x-code-generator::modal>
</div>