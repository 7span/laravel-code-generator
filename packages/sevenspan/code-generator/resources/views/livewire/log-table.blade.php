<div class="p-6">
    <!-- Logs Table -->
    <div class="bg-white rounded-lg shadow">
        {{--  div for horizontal scrolling of the table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 table-fixed">
                <colgroup>
                    {{-- Define explicit column widths. Adjust percentages as needed. Total should be 100%. --}}
                    {{-- These are more aggressive to force wrapping. --}}
                    <col style="width: 10%;"> {{-- Type --}}
                    <col style="width: 30%;"> {{-- File Path --}}
                    <col style="width: 10%;"> {{-- Status --}}
                    <col style="width: 35%;"> {{-- Message --}}
                    <col style="width: 15%;"> {{-- Date --}}
                </colgroup>
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-red-500 uppercase tracking-wider">File Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-red-500 uppercase tracking-wider">File Path</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-red-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-red-500 uppercase tracking-wider">Message</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-red-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900 align-top break-words"> {{-- break-words is good for type --}}
                                {{ $log->file_type }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 align-top"> {{-- break-all for file paths as they often have no spaces --}}
                                {{ $log->file_path }}
                            </td>
                            <td class="px-6 py-4 align-top">
                                @php
                                    $statusValue = $log->status;
                                    if (is_object($statusValue) && enum_exists(get_class($statusValue)) && property_exists($statusValue, 'value')) {
                                        $statusValue = $statusValue->value;
                                    }
                                    $statusValue = (string) $statusValue;
                                    $statusClass = match ($statusValue) {
                                        'success' => 'bg-green-100 text-green-800',
                                        'error'   => 'bg-red-100 text-red-800',
                                        'warning' => 'bg-yellow-100 text-yellow-800',
                                        default   => 'bg-gray-100 text-gray-800',
                                    };
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                    {{ ucfirst($statusValue) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 align-top break-words"> {{-- break-words for messages --}}
                                {{ $log->message }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap align-top">
                                {{ $log->created_at->format('Y-m-d H:i:s') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                No logs found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>