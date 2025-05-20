<div x-data="{ crudFile: @entangle('crudFile').live }">
    <!-- Files Section -->
    <div class="mb-6">
        <h2 class="text-sm font-medium mb-2">Files:</h2>
        <div class="flex flex-wrap gap-4">
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="modelFile">
                <span class="ml-2 text-sm">Model</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="migrationFile">
                <span class="ml-2 text-sm">Migration</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="crudFile">
                <span class="ml-2 text-sm">Admin CRUD</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="policyFile">
                <span class="ml-2 text-sm">Policy</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="observerFile">
                <span class="ml-2 text-sm">Observer</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="serviceFile">
                <span class="ml-2 text-sm">Service</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="notificationFile">
                <span class="ml-2 text-sm">Notification</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="resourceFile">
                <span class="ml-2 text-sm">Resource</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="requestFile">
                <span class="ml-2 text-sm">Request</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="factoryFile">
                <span class="ml-2 text-sm">Factory</span>
            </label>
        </div>
    </div>
    <div class="mb-6">
    <h2 class="text-sm font-medium mb-2">Features:</h2>
    <div class="flex flex-wrap gap-4">
    <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="softDeleteFile">
                <span class="ml-2 text-sm">Soft Delete</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="overwriteFiles">
                <span class="ml-2 text-sm">Overwrite</span>
            </label>
</div>
</div>
    <!-- Traits Section -->
    <div class="mb-6">
        <h2 class="text-sm font-medium mb-2">Traits:</h2>
        <div class="flex flex-wrap gap-4">
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="ApiResponse">
                <span class="ml-2 text-sm">ApiResponse</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="BaseModel">
                <span class="ml-2 text-sm">BaseModel</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="BootModel">
                <span class="ml-2 text-sm">BootModel</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="PaginationTrait">
                <span class="ml-2 text-sm">PaginationTrait</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="ResourceFilterable">
                <span class="ml-2 text-sm">ResourceFilterable</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="HasUuid">
                <span class="ml-2 text-sm">HasUuid</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="HasUserAction">
                <span class="ml-2 text-sm">HasUserAction</span>
            </label>
        </div>
    </div>

    <!-- Methods Section -->
    <div class="mb-6" x-show="!crudFile">
        <h2 class="text-sm font-medium mb-2">Methods:</h2>
        <div class="flex flex-wrap gap-4">
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="index">
                <span class="ml-2 text-sm">Index</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="store">
                <span class="ml-2 text-sm">Store</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="show">
                <span class="ml-2 text-sm">Show</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="update">
                <span class="ml-2 text-sm">Update</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="destroy">
                <span class="ml-2 text-sm">Destroy</span>
            </label>
        </div>
        @if($errorMessage)
        <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-md">
            <p class="text-base font-medium text-red-600">{{ $errorMessage }}</p>
        </div>
        @endif
    </div>
</div>