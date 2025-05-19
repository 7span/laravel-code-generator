<div wire:show="isRelDeleteModalOpen" x-data x-transition.duration.200ms
    x-on:click.self="$wire.isRelDeleteModalOpen=false"
    class="fixed top-0 left-0 flex items-center justify-center w-full h-full bg-gray-500 bg-opacity-50 z-50">

    <x-code-generator::modal modalTitle="Are you Sure?">
        <!-- Modal header -->
        <x-slot:closebtn>
            <button x-on:click="$wire.isRelDeleteModalOpen=false"
                class="text-gray-500 hover:text-black text-xl">&times;</button>
        </x-slot:closebtn>
            <div class="mt-4 space-y-4">
                Delete This Field.
            </div>

        <!-- Modal footer -->
        <x-slot:footer>
            <x-code-generator::button wire:click="deleteRelation" title="Delete" />
        </x-slot:footer>
    </x-modal>
</div>