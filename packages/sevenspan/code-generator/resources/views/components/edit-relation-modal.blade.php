<div wire:show="isRelEditModalOpen" x-data="{ relationType: @entangle('relation_type').live }"
    x-transition.duration.200ms x-on:click.self="$wire.isRelEditModalOpen=false"
    class="fixed top-0 left-0 flex items-center justify-center w-full h-full bg-gray-500 bg-opacity-50 z-50">

    <x-code-generator::modal modalTitle=" Edit Eloquent Relation">

        <!-- Modal header -->
        <x-slot:closebtn>
            <button x-on:click="$wire.isRelEditModalOpen=false"
                class="text-gray-500 hover:text-black text-xl">&times;</button>
        </x-slot:closebtn>
        <div class="flex flex-col gap-4">
            <div class="flex flex-col">
                <select class="w-full p-2 border border-gray-300 rounded-md" wire:model.live="relation_type">
                    <x-code-generator::relation-option />
                </select>
                @error('relation_type')
                <span class="text-red-600 text-sm block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="space-y-3">
                <div class="flex gap-2">
                    <!-- Relation Type -->
                    <div class="w-1/2">
                        <input type="text" id="relatedModel" placeholder="Model Name" wire:model.live="related_model"
                            class="w-full p-2 border border-gray-300 rounded-md placeholder:text-base">
                        @error('model_name')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Second Model -->
                    <div class="w-1/2">
                        <input type="text" wire:model.live="second_model" placeholder="Second Model"
                            class="w-full p-2 border border-gray-300 rounded-md placeholder:text-base"
                            :disabled="!['Has One Through', 'Has Many Through'].includes(relationType)"
                            :class="{ 'bg-gray-100 text-gray-400': !['Has One Through', 'Has Many Through'].includes(relationType) }" />
                        @error('second_model')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="flex gap-2">
                    <!-- Foreign Key -->
                    <div class="w-1/2">
                        <input type="text" placeholder="Foreign Key"
                            class="w-full p-2 border border-gray-300 rounded-md placeholder:text-base"
                            wire:model.live="foreign_key" />
                        @error('foreign_key')
                        <span class="text-red-600 text-sm block mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Local Key -->
                    <div class="w-1/2">
                        <input type="text" placeholder="Local Key"
                            class="w-full p-2 border border-gray-300 rounded-md placeholder:text-base"
                            wire:model.live="local_key" />
                        @error('local_key')
                        <span class="text-red-600 text-sm block mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

        </div>
        <!-- Modal footer -->
        <x-slot:footer>
            <div class="mr-6">
            <x-code-generator::button title="Cancel" x-on:click="$wire.isRelEditModalOpen=false"/>
          </div>
          <x-code-generator::button wire:click="addRelation" title="Update" />
        </x-slot:footer>
        </x-modal>
</div>