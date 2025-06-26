@props(['isEditing' => false])

<div wire:show="isNotificationModalOpen" x-data x-cloak x-transition.duration.200ms
    class="fixed top-0 left-0 flex items-center justify-center w-full h-full bg-gray-500 bg-opacity-50 z-50"
    x-on:click.self="$wire.isNotificationModalOpen=false">

    <x-code-generator::modal :modalTitle="$isEditing ? 'Update Notification' : 'Add Notification'">
        <x-slot:closebtn>
            <button x-on:click="$wire.isNotificationModalOpen=false"
                class="text-gray-500 hover:text-black text-xl">&times;</button>
        </x-slot:closebtn>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Class Name</label>
                <input wire:model.live="class_name" type="text" placeholder="Enter name"
                    class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-400" />
                @error('class_name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Data</label>
                <input wire:model.live="data" type="text" placeholder="Enter Data"
                    class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-400" />
                @error('data') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                <p class="text-xs text-gray-400 mt-1">Example: id,name</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Subject</label>
                <input wire:model.live="subject" type="text" placeholder="Enter Subject"
                    class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-400" />
                @error('subject') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>


    <div>
      <label class="block text-sm font-medium text-gray-700 flex items-center gap-1">
                  Blade View Path 
            <div class="relative group cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <title>Tooltip</title>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
                </svg>
                <div class="absolute left-6 top-0 w-64 bg-gray-700 text-white text-sm rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                    Notification file will be added in <code>resources/views/</code> folder. If you will enter folder/blade file name then folder will be created first and then blade file will be created inside that folder.
                </div>
            </div>
        </label>

    <input wire:model.live="notification_blade_path" type="text" placeholder="users/email"
        class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-400" />
        @error('notification_blade_path') 
            <span class="text-red-600 text-sm">{{ $message }}</span> 
        @enderror
    </div>
        <x-slot:footer>
            <div class="mr-6">
                <x-code-generator::button title="Cancel" x-on:click="$wire.isNotificationModalOpen=false" />
            </div>
            <x-code-generator::button wire:click="saveNotification" :title="$isEditing ? 'Update' : 'Add' " />
        </x-slot:footer>
        </x-code-generator::modal>
</div>