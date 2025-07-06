<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('dashboard.save-payments') }}">
                        @csrf
                        <table class="table-auto w-full border-collapse border border-gray-400 mb-4">
                            <thead>
                                <tr>
                                    <th>Invoice ID</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Received</th>
                                    <th>Due</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $grandTotalDue = 0; @endphp
                                @foreach ($invoices as $invoice)
                                    @php
                                        $due = $invoice->total_amount - $invoice->received_amount;
                                        $grandTotalDue += $due;
                                    @endphp
                                    <tr>
                                        <td>{{ $invoice->id }}</td>
                                        <td>{{ \Carbon\Carbon::parse($invoice->created_at)->format('d M Y') }}</td>
                                        <td>{{ $invoice->total_amount }}</td>
                                        <td>
                                            <input type="number" name="received_amounts[{{ $invoice->id }}]" value="{{ $invoice->received_amount }}" step="0.01" class="border px-2 py-1 w-24">
                                        </td>
                                        <td>{{ number_format($due, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="font-bold text-right mb-4">Grand Total Due: ₹{{ number_format($grandTotalDue, 2) }}</div>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save Payments</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
