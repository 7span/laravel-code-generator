<div class="mb-6">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-4 py-2 text-left text-sm">Column</th>
                    <th class="px-4 py-2 text-left text-sm">Data Type</th>
                    <th class="px-4 py-2 text-left text-sm">Validation</th>
                    <th class="px-4 py-2 text-left text-sm">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="px-4 py-2 text-gray-600">name</td>
                    <td class="px-4 py-2 text-gray-600">string</td>
                    <td class="px-4 py-2 text-gray-600">required</td>
                    <td class="px-4 py-2 text-gray-600">
                        <button>
                            <x-code-generator::delete-svg />
                        </button>
                         <button>
                            <x-code-generator::edit-svg />
                        </button>
                    </td>
                </tr>
                  <tr>
                    <td class="px-4 py-2 text-gray-600">category</td>
                    <td class="px-4 py-2 text-gray-600">string</td>
                    <td class="px-4 py-2 text-gray-600">optional</td>
                    <td class="px-4 py-2 text-gray-600">
                        <button>
                            <x-code-generator::delete-svg />
                        </button>
                         <button>
                            <x-code-generator::edit-svg />
                        </button>
                    </td>
                </tr>
                @foreach ($fieldsData as $field)
                <tr class=" even:bg-gray-100 ">
                    <td class="px-4 py-2">{{$field['column_name']}}</td>
                    <td class="px-4 py-2">{{$field['data_type']}}</td>
                    <td class="px-4 py-2">{{$field['column_validation']}}</td>
                    <td class="px-4 py-2">
                        <button wire:click="openDeleteFieldModal('{{ $field['id']}}')">
                            <x-code-generator::delete-svg />
                        </button>
                        <button wire:click="openEditFieldModal('{{ $field['id']}}')">
                            <x-code-generator::edit-svg />
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>