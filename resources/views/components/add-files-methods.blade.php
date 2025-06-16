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
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="is_admin_crud_added">
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
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="is_notification_file_added">
                    <span class="ml-2 text-sm">Notification</span>
                </label>
            </div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="is_select_all_files_checked">
                    <span class="ml-2 text-sm">Select All Files</span>
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
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="is_index_method_added">
                <span class="ml-1 text-sm">Index</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="is_store_method_added">
                <span class="ml-1 text-sm">Store</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="is_show_method_added">
                <span class="ml-1 text-sm">Show</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="is_update_method_added">
                <span class="ml-1 text-sm">Update</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="is_destroy_method_added">
                <span class="ml-1 text-sm">Destroy</span>
            </label>
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="is_select_all_methods_checked">
                    <span class="ml-2 text-sm">Select All Methods</span>
                </label>
        </div>
    </div>

    <h2 class="text-sm font-medium mb-2">Which traits do you want to include?</h2>
    <div class="grid grid-cols-3 mb-6 gap-6 border-b border-gray-300">
        <div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="is_boot_model_trait_added">
                    <span class="ml-2 text-sm">BootModel.php</span>
                </label>
            </div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="is_pagination_trait_added">
                    <span class="ml-2 text-sm">PaginationTrait.php</span>
                </label>
            </div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="is_resource_filterable_trait_added">
                    <span class="ml-2 text-sm">ResourceFilterable.php</span>
                </label>
            </div>
        </div>
        <div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="is_has_uuid_trait_added">
                    <span class="ml-2 text-sm">HasUuid.php</span>
                </label>
            </div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="is_has_user_action_trait_added">
                    <span class="ml-2 text-sm">HasUserAction.php</span>
                </label>
            </div>
            <div class="mb-2">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="is_select_all_traits_checked">
                    <span class="ml-2 text-sm">Select All Traits</span>
                </label>
            </div>
        </div>
    </div>

    <h2 class="text-sm font-medium mb-2">General Settings:</h2>
    <div class="mb-6 flex space-x-4 border-b border-gray-200">
        <label class="flex items-center mb-2">
            <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="is_overwrite_files">
            <span class="ml-1 text-sm">Overwrite Files</span>
        </label>
        <label class="flex items-center mb-2">
            <input type="checkbox" class="form-checkbox h-4 w-4 text-red-500" wire:model.live="is_soft_delete_added">
            <span class="ml-1 text-sm">Soft Delete</span>
        </label>
    </div>
</div>