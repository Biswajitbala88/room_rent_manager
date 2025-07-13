<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tenants
        </h2>
    </x-slot>

    <div class="py-10 px-4 max-w-7xl mx-auto">
        <div class="mb-4">
            <a href="{{ route('tenants.create') }}"
                class="inline-block px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium">
                + Add Tenant
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto bg-white rounded shadow p-3">
            <table id="tenants-table" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100 text-gray-700 text-left text-sm uppercase">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Room</th>
                        <th class="px-4 py-3">Phone</th>
                        <th class="px-4 py-3">Start Date</th>
                        <th class="px-4 py-3">Rent</th>
                        <th class="px-4 py-3">Aadhaar</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Include Water Charge</th>
                        @if ( auth()->user()->user_type == 'SA' )
                        <th class="px-4 py-3">Owner</th>
                        @endif
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @foreach ($tenants as $key => $tenant)
                        <tr class="border-b">
                            <td class="px-4 py-2">{{ $key + 1 }}</td>
                            <td class="px-4 py-2">{{ $tenant->name }}</td>
                            <td class="px-4 py-2">
                                <span class="inline-block px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                   Room-{{ $tenant->room_no }}
                                </span>
                            </td>
                            <td class="px-4 py-2">{{ $tenant->phone }}</td>
                            <td class="px-4 py-2">{{ $tenant->start_date }}</td>
                            <td class="px-4 py-2">{{ $tenant->rent_amount }}</td>
                            <td class="px-4 py-2">
                                @php
                                    $aadhaarImages = json_decode($tenant->aadhaar_image, true);
                                @endphp

                                @if (!empty($aadhaarImages) && is_array($aadhaarImages))
                                    @foreach ($aadhaarImages as $imgPath)
                                        <img src="{{ asset('storage/' . $imgPath) }}" class="w-10 h-10 object-cover rounded inline-block mr-1 mb-1" />
                                    @endforeach
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                @if(strtolower($tenant->status) === 'active')
                                    <span class="inline-block px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                        {{ ucfirst($tenant->status) }}
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">
                                        {{ ucfirst($tenant->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                @if($tenant->is_water_charge)
                                    <span class="inline-block px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Yes</span>
                                @else
                                    <span class="inline-block px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">No</span>
                                @endif
                            </td>
                            @if ( auth()->user()->user_type == 'SA' )
                            <td class="px-4 py-2">
                                {{ $tenant->parentUser->name ?? '' }}
                            </td>
                            @endif
                            <td class="px-4 py-2 space-x-2">
                                <a href="{{ route('tenants.edit', $tenant) }}" class="text-blue-600 hover:underline">Edit</a>
                                <form action="{{ route('tenants.destroy', $tenant) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- DataTables JS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#tenants-table').DataTable({
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search tenants...",
                }
            });
        });
    </script>
</x-app-layout>
