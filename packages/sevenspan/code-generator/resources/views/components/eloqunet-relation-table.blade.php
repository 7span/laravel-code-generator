<div class="overflow-x-auto px-0 py-0">
    <table class="w-full border-collapse border border-gray-200">

    <!-- Table Header -->
        <thead class="bg-gray-200 rounded-t-lg">
            <th class="p-3 text-left text-sm">Related Model</th>
            <th class="p-3 text-left text-sm">Data Type</th>
            <th class="p-3 text-left text-sm">Second Model</th>
            <th class="p-3 text-left text-sm">Foreign Key</th>
            <th class="p-3 text-left text-sm">Local Key</th>
            <th class="p-3 text-left text-sm">Action</th>
            <th class="p-3 text-left text-sm">Second foreign key</th>
            <th class="p-3 text-left text-sm">Second local key</th>
        </thead>
        <tbody>
            <!-- Added Relations Content -->
            @if($relationData)
            @foreach ($relationData as $relation)
            <tr class="border">
                <td class="px-4 py-2 text-gray-600">{{$relation['related_model']}}</td>
                <td class="px-4 py-2 text-gray-600">{{$relation['relation_type']}}</td>
                <td class="px-4 py-2 text-gray-600">{{$relation['second_model'] ?? '-'}}</td>
                <td class="px-4 py-2 text-gray-600">{{$relation['foreign_key']}}</td>
                <td class="px-4 py-2 text-gray-600">{{$relation['local_key']}}</td>
                <td class="px-4 py-2 text-gray-600">{{$relation['via_foreign_key'] ?? '-'}}</td>
                <td class="px-4 py-2 text-gray-600">{{$relation['via_local_key'] ?? '-'}}</td>
                <td class="px-4 py-2 flex items-center">
                    <button wire:click="openDeleteModal('{{ $relation['id'] }}')" class="text-red-500">
                        <x-code-generator::delete-svg />
                    </button>
                    <button wire:click="openEditRelationModal('{{ $relation['id'] }}')" class="text-red-500">
                        <x-code-generator::edit-svg />
                    </button>
                </td>
            </tr>
            @endforeach
            @else
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                    Any relationship not added yet.
                </td>
            </tr>
            @endif
        </tbody>
    </table>
</div>