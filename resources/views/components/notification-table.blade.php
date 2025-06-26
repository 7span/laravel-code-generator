@props(['notificationData'])

<div class="overflow-x-auto px-0 py-0">
    <table class="w-full border-collapse border border-gray-200">

        <!-- Table Header -->
        <thead class="bg-gray-200 rounded-t-lg">
            <th class="p-3 text-left text-sm">Class Name</th>
            <th class="p-3 text-left text-sm">Data</th>
            <th class="p-3 text-left text-sm">Subject</th>
            <th class="p-3 text-left text-sm">Blade View Path</th>
            <th class="p-3 text-left text-sm">Action</th>
        </thead>
        <tbody>
            <!-- Added Relations Content -->
            @if($notificationData)
            @foreach ($notificationData as $notification)
            <tr class="border">
                <td class="px-4 py-2 text-gray-600">{{$notification['class_name']}}</td>
                <td class="px-4 py-2 text-gray-600">{{$notification['data']}}</td>
                <td class="px-4 py-2 text-gray-600">{{$notification['subject']}}</td>
                <td class="px-4 py-2 text-gray-600">{{$notification['notification_blade_path']}}</td>
                <td class="px-4 py-2 flex items-center">
                <button wire:click="openEditNotificationModal('{{ $notification['id'] }}')" class="text-red-500">
                        <x-code-generator::delete-svg />
                <button wire:click="openEditNotificationModal('{{ $notification['id'] }}')" class="text-red-500">
                        <x-code-generator::edit-svg />
                </button>
                </td>
            </tr>
            @endforeach
            @else
            <tr>
                <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                    Any notification file not added yet.
                </td>
            </tr>
            @endif
        </tbody>
    </table>
</div>