<div>
    <h2 class="text-sm font-medium mb-2">Which files do you want to include?</h2>
    <div class="grid grid-cols-3 mb-6 gap-6 border-b border-gray-300">
        <div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="is_model_file_added">
                    <span class="ml-2 text-sm">Model</span>
                </label>
            </div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="is_migration_file_added">
                    <span class="ml-2 text-sm">Migration</span>
                </label>
            </div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="is_crud_file_added">
                    <span class="ml-2 text-sm">Admin CRUD Controller</span>
                </label>
            </div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="is_policy_file_added">
                    <span class="ml-2 text-sm">Policy</span>
                </label>
            </div>
        </div>
        <div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="is_observer_file_added">
                    <span class="ml-2 text-sm">Observer</span>
                </label>
            </div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="is_service_file_added">
                    <span class="ml-2 text-sm">Service</span>
                </label>
            </div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500"
                        wire:model.live="is_notification_file_added">
                    <span class="ml-2 text-sm">Notification</span>
                </label>
            </div>
        </div>
        <div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="is_resource_file_added">
                    <span class="ml-2 text-sm">Resource</span>
                </label>
            </div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="is_request_file_added">
                    <span class="ml-2 text-sm">Request</span>
                </label>
            </div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="is_factory_file_added">
                    <span class="ml-2 text-sm">Factory</span>
                </label>
            </div>
        </div>
    </div>
    <div class="mb-6">
        <h2 class="text-sm font-medium mb-2 ">Which method do you want to include in API Controller?</h2>
        <div class="flex space-x-4 mb-6">
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

    <h2 class="text-sm font-medium mb-2">Which traits do you want to include?</h2>
    <div class="grid grid-cols-3 mb-6 gap-6 border-b border-gray-300">
        <div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="boot_model">
                    <span class="ml-2 text-sm">BootModel.php</span>
                </label>
            </div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="pagination_trait">
                    <span class="ml-2 text-sm">PaginationTrait.php</span>
                </label>
            </div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500"
                        wire:model.live="resource_filterable_trait">
                    <span class="ml-2 text-sm">ResourceFilterable.php</span>
                </label>
            </div>
        </div>
        <div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="has_uuid">
                    <span class="ml-2 text-sm">HasUuid.php</span>
                </label>
            </div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="has_user_action">
                    <span class="ml-2 text-sm">HasUserAction.php</span>
                </label>
            </div>
        </div>
    </div>

    <h2 class="text-sm font-medium mb-2">General Settings:</h2>
    <div class="mb-6 flex space-x-4 border-b border-gray-200">
        <label class="flex items-center mb-2">
            <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="is_overwrite_files_added">
            <span class="ml-1 text-sm">Overwrite Files</span>
        </label>
        <label class="flex items-center mb-2">
            <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="is_soft_delete_added">
            <span class="ml-1 text-sm">Soft Delete</span>
        </label>
    </div>
</div>