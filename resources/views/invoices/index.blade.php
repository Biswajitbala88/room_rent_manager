<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Invoices
        </h2>
    </x-slot>

    <div class="py-10 px-4 max-w-7xl mx-auto">
        <div class="mb-4">
            <a href="{{ route('invoices.create') }}"
                class="inline-block px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium">
                + Add Invoice
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto bg-white rounded shadow p-3">
            <table id="invoices-table" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100 text-gray-700 text-left text-sm uppercase">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Tenant</th>
                        <th class="px-4 py-3">Month</th>
                        <th class="px-4 py-3">Total Electricity Units</th>
                        <th class="px-4 py-3">Sum Electricity Units</th>
                        <th class="px-4 py-3">Electricity Charge</th>
                        <th class="px-4 py-3">Water Charge</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse ($invoices as $key => $invoice)
                        <tr class="border-b text-sm">
                            <td class="px-4 py-2">{{ $key + 1 }}</td>
                            <td class="px-4 py-2">{{ $invoice->tenant->name }}</td>
                            <td class="px-4 py-2">{{ $invoice->month }}</td>
                            <td class="px-4 py-2">{{ $invoice->electricity_units }}</td>
                            <td class="px-4 py-2">{{ $invoice->sum_electricity_units }}</td>
                            <td class="px-4 py-2">₹{{ number_format($invoice->electricity_charge, 2) }}</td>
                            <td class="px-4 py-2">₹{{ number_format($invoice->water_charge, 2) }}</td>
                            <td class="px-4 py-2 font-semibold text-blue-600">₹{{ number_format($invoice->total_amount, 2) }}</td>
                            <td class="px-4 py-2">
                                @if(strtolower($invoice->status) === 'paid')
                                    <span class="inline-block px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-2 space-x-2">
                                <a href="{{ route('invoices.edit', $invoice) }}" class="text-blue-600 hover:underline">Edit</a>
                                <a href="{{ route('invoices.download', $invoice) }}" class="text-indigo-600 hover:underline">PDF</a>
                                <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-gray-500">No invoices found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- DataTables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#invoices-table').DataTable({
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search invoices...",
                }
            });
        });
    </script>
</x-app-layout>
