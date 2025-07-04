<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Invoices') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto">
            <a href="{{ route('invoices.create') }}"
            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Add Invoice
            </a>

            @if (session('success'))
                <div class="text-green-600 mt-4">{{ session('success') }}</div>
            @endif

            <table class="w-full mt-6 bg-white rounded shadow">
                <thead>
                    <tr class="text-center">
                        <th class="px-4 py-2">Tenant</th>
                        <th>Month</th>
                        <th>Electricity Units</th>
                        <th>Electricity Charge</th>
                        <th>Water Charge</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="px-4 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                    <tr class="text-center">
                        <td class="px-4 py-2">{{ $invoice->tenant->name }}</td>
                        <td>{{ $invoice->month }}</td>
                        <td>{{ $invoice->electricity_units }}</td>
                        <td>₹{{ number_format($invoice->electricity_charge, 2) }}</td>
                        <td>₹{{ number_format($invoice->water_charge, 2) }}</td>
                        <td class="font-semibold text-blue-600">₹{{ number_format($invoice->total_amount, 2) }}</td>
                        <td>
                            @if($invoice->status === 'paid')
                                <span class="text-green-600 font-semibold">Paid</span>
                            @else
                                <span class="text-red-600 font-semibold">Unpaid</span>
                            @endif
                        </td>
                        <td class="flex justify-center gap-2 py-2">
                            <a href="{{ route('invoices.edit', $invoice) }}" class="text-blue-600">Edit</a>
                            <a href="{{ route('invoices.download', $invoice) }}" class="text-indigo-600 hover:underline">Download PDF</a>
                            <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach

                    @if($invoices->isEmpty())
                        <tr>
                            <td colspan="8" class="text-center py-4 text-gray-500">No invoices found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            
        </div>
    </div>
</x-app-layout>
