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
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="crudFile">
                    <span class="ml-2 text-sm">Admin CRUD</span>
                </label>
            </div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="policyFile">
                    <span class="ml-2 text-sm">Policy File</span>
                </label>
            </div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="observerFile">
                    <span class="ml-2 text-sm">Observer File</span>
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
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="factoryFile">
                    <span class="ml-2 text-sm">Factory File </span>
                </label>
            </div>
        </div>
        <h2 class="text-sm font-medium mb-2">Which Traits do you want to include?</h2>
        <div class="flex space-x-4">
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="ApiResponse">
                <span class="ml-1 text-sm">ApiResponse.php</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="BaseModel">
                <span class="ml-1 text-sm">BaseModel.php</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="BootModel">
                <span class="ml-1 text-sm">BootModel.php</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="PaginationTrait">
                <span class="ml-1 text-sm">PaginationTrait.php</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="ResourceFilterable">
                <span class="ml-1 text-sm">ResourceFilterable.php</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="HasUuid">
                <span class="ml-1 text-sm">HasUuid.php</span>
            </label>
             <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="HasUserAction">
                <span class="ml-1 text-sm">HasUserAction.php</span>
            </label>
        </div>
        <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="overwriteFiles">
                    <span class="ml-2 text-sm">Overwrite Files?</span>
                </label>
        </div>
        <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="softDeleteFile">
                    <span class="ml-2 text-sm">Soft Delete</span>
                </label>
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