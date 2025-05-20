<div x-data="{ crudFile: @entangle('crudFile').live }">
    <!-- Options -->
<<<<<<< HEAD
     <h2 class="text-sm font-medium mb-2">Which Files do you want to include?</h2>
    <div class="grid grid-cols-3 mb-6 gap-6 border-b border-gray-300">
=======
    <h2 class="text-sm font-medium mb-2">Which Files do you want to include?</h2>
    <div class="grid grid-cols-3 gap-6 border-b border-gray-300">
>>>>>>> 17ce72fb4fae5b8d87243176510c3e412a861d8f
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
                    <span class="ml-2 text-sm">Admin CRUD Control</span>
                </label>
            </div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="policyFile">
                    <span class="ml-2 text-sm">Policy</span>
                </label>
            </div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="observerFile">
                    <span class="ml-2 text-sm">Observer</span>
                </label>
            </div>
        </div>
        <div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="serviceFile">
                    <span class="ml-2 text-sm">Service</span>
                </label>
            </div>
            <div class="mb-2">
                <label class="flex items-center">
<<<<<<< HEAD
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="notificationFile">
                    <span class="ml-2 text-sm">Notification</span>
=======
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500"
                        wire:model.live="notificationFile">
                    <span class="ml-2 text-sm">Notification File</span>
>>>>>>> 17ce72fb4fae5b8d87243176510c3e412a861d8f
                </label>
            </div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="resourceFile">
                    <span class="ml-2 text-sm">Resource</span>
                </label>
            </div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="requestFile">
                    <span class="ml-2 text-sm">Request</span>
                </label>
            </div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="factoryFile">
                    <span class="ml-2 text-sm">Factory</span>
                </label>
            </div>
        </div>
        <div>
        <h2 class="text-sm font-medium">Traits:</h2>
        <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="ApiResponse">
                    <span class="ml-2 text-sm">ApiResponse.php</span>
                </label>
        </div>
        <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="BaseModel">
                    <span class="ml-2 text-sm">BaseModel.php</span>
                </label>
         </div>
        <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="BootModel">
                    <span class="ml-2 text-sm">BootModel.php</span>
                </label>
        </div>
        <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="PaginationTrait">
                    <span class="ml-2 text-sm">PaginationTrait.php</span>
                </label>
        </div>
        <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500"
                        wire:model.live="ResourceFilterable">
                    <span class="ml-2 text-sm">ResourceFilterable.php</span>
                </label>
        </div>
        <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="HasUuid">
                    <span class="ml-2 text-sm">HasUuid.php</span>
                </label>
        </div>
        <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="HasUserAction">
                    <span class="ml-2 text-sm">HasUserAction.php</span>
                </label>
<<<<<<< HEAD
        </div>
        </div>       
     </div>
        <h2 class="text-sm font-medium mb-2">Features:</h2>
        <div class="mb-6 flex space-x-4">
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="overwriteFiles">
                <span class="ml-1 text-sm">Overwrite Files</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="softDeleteFile">
                <span class="ml-1 text-sm">Soft Delete</span>
            </label>
=======
            </div>
>>>>>>> 17ce72fb4fae5b8d87243176510c3e412a861d8f
        </div>
    </div>
    <h2 class="text-sm font-medium mb-2">Features:</h2>
    <div class="mb-6 flex space-x-4">
        <label class="flex items-center">
            <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="overwriteFiles">
            <span class="ml-1 text-sm">Overwrite Files</span>
        </label>
        <label class="flex items-center">
            <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="softDeleteFile">
            <span class="ml-1 text-sm">Soft Delete</span>
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
      
    </div>
</div>