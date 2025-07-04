<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tenants') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto">
            <a href="{{ route('tenants.create') }}"
            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Add Tenant
            </a>

            @if (session('success'))
                <div class="text-green-600 mt-4">{{ session('success') }}</div>
            @endif

            <table class="w-full mt-6 bg-white rounded shadow">
                <thead>
                    <tr>
                        <th class="px-4 py-2">Name</th>
                        <th>Room</th>
                        <th>Phone</th>
                        <th>Start Date</th>
                        <th>Rent</th>
                        <th>Image</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tenants as $tenant)
                    <tr class="text-center border-t">
                        <td class="px-4 py-2">{{ $tenant->name }}</td>
                        <td>{{ $tenant->room_no }}</td>
                        <td>{{ $tenant->phone }}</td>
                        <td>{{ $tenant->start_date }}</td>
                        <td>{{ $tenant->rent_amount }}</td>
                        <td>
                            @if ($tenant->aadhaar_image)
                                <img src="{{ asset('storage/' . $tenant->aadhaar_image) }}" class="w-16 h-16 mx-auto rounded" />
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ $tenant->status }}</td>
                        <td class="flex justify-center gap-2 py-2">
                            <a href="{{ route('tenants.edit', $tenant) }}" class="text-blue-600">Edit</a>
                            <form action="{{ route('tenants.destroy', $tenant) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $tenants->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
