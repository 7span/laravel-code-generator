<div x-data="{ crudFile: @entangle('crudFile').live }">
    <!-- Options -->
     <h2 class="text-sm font-medium mb-2">Which Files do you want to include?</h2>
    <div class="grid grid-cols-3 gap-6 mb-6 border-b border-gray-300">
        <div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="modelFile">
                    <span class="ml-2 text-sm">Model</span>
                </label>
            </div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="migrationFile">
                    <span class="ml-2 text-sm">Migration</span>
                </label>
            </div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="softDeleteFile">
                    <span class="ml-2 text-sm">Soft Delete</span>
                </label>
            </div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="crudFile">
                    <span class="ml-2 text-sm">Admin CRUD</span>
                </label>
            </div>
        </div>
        <div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="serviceFile">
                    <span class="ml-2 text-sm">Service File</span>
                </label>
            </div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="notificationFile">
                    <span class="ml-2 text-sm">Notification File</span>
                </label>
            </div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="resourceFile">
                    <span class="ml-2 text-sm">Resource File</span>
                </label>
            </div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="requestFile">
                    <span class="ml-2 text-sm">Request File</span>
                </label>
            </div>
        </div>
        <div class="mb-6">
            <div class="flex items-start mb-2">
                <input type="checkbox" class="form-checkbox h-4 w-4 mt-1 text-red-500" wire:model.live="traitFiles">
                <div class="ml-2">
                    <span class="text-sm">Trait Files</span>
                    <div class="text-xs ml-2 text-gray-600">
                        <div>ApiResponse.php</div>
                        <div>BaseModel.php</div>
                        <div>BootModel.php</div>
                        <div>PaginationTrait.php</div>
                        <div>ResourceFilterable.php</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
     <div class="mb-6" x-show="!crudFile">
        <h2 class="text-sm font-medium mb-2">Which method do you want to include?</h2>
        <div class="flex space-x-4">
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="index">
                <span class="ml-1 text-sm">Index</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="store">
                <span class="ml-1 text-sm">Store</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="show">
                <span class="ml-1 text-sm">Show</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="update">
                <span class="ml-1 text-sm">Update</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="destroy">
                <span class="ml-1 text-sm">Destroy</span>
            </label>
        </div>
        @if($errorMessage)
        <p class="text-xs text-red-500 mt-1">{{ $errorMessage }}</p>
        @endif
    </div>
</div>