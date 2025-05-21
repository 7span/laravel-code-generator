<div wire:show="isAddRelModalOpen" x-data="{ relationType: @entangle('relation_type').live }"
    x-transition.duration.200ms
    class="fixed top-0 left-0 flex items-center justify-center w-full h-full bg-gray-500 bg-opacity-50 z-50"
    x-on:click.self="$wire.isAddRelModalOpen=false">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl p-6 relative">
        <!-- Modal header -->
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Add Eloquent Relation</h2>
            <button x-on:click="$wire.isAddRelModalOpen=false" class="text-gray-500 hover:text-black text-xl">
                &times;
            </button>
        </div>

        <!-- Modal body -->
        <div class="flex flex-col gap-4">
            <!-- Relation type -->
            <div class="flex flex-col">
                <select class="w-full p-2 border border-gray-300 rounded-md" wire:model.live="relation_type">
                    <x-code-generator::relation-option />
                </select>
                @error('relation_type')
                <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Related and second model -->
            <div class="flex gap-2">
                <div class="w-1/2">
                    <input type="text" placeholder="Model Name"
                        class="w-full p-2 border border-gray-300 rounded-md placeholder:text-base"
                        wire:model.live="related_model" />
                    @error('related_model')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="w-1/2">
                    <input type="text" placeholder="Second Model"
                        class="w-full p-2 border border-gray-300 rounded-md placeholder:text-base"
                        wire:model.live="second_model"
                        :disabled="!['Has One Through', 'Has Many Through'].includes(relationType)"
                        :class="{ 'bg-gray-100 text-gray-400': !['Has One Through', 'Has Many Through'].includes(relationType) }" />
                    @error('second_model')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Foreign/Local Keys -->
            <div class="flex gap-2">
                <div class="w-1/2">
                    <input type="text" placeholder="Foreign Key"
                        class="w-full p-2 border border-gray-300 rounded-md placeholder:text-base"
                        wire:model.live="foreign_key" />
                    @error('foreign_key')
                    <span class="block mt-1 text-sm text-red-600">{{ $message }}</span>
                    @enderror
                </div>

                <div class="w-1/2">
                    <input type="text" placeholder="Local Key"
                        class="w-full p-2 border border-gray-300 rounded-md placeholder:text-base"
                        wire:model.live="local_key" />
                    @error('local_key')
                    <span class="block mt-1 text-sm text-red-600">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Modal footer -->
        <div class="mt-6 flex justify-end gap-4">
            <x-code-generator::button title="Cancel" x-on:click="$wire.isAddRelModalOpen=false" />
            <x-code-generator::button wire:click="addRelation" title="Add" />
        </div>
    </div>
</div>