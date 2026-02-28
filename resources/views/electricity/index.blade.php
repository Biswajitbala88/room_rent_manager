<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Electricity Consumption Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 shadow">
                    <div class="text-sm font-bold">Total Units (Filtered)</div>
                    <div class="text-2xl font-semibold">{{ $totalUnits }} Units</div>
                </div>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 shadow">
                    <div class="text-sm font-bold">Total Electricity Cost (Filtered)</div>
                    <div class="text-2xl font-semibold">₹{{ number_format($totalCharge, 2) }}</div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('electricity.index') }}" class="flex flex-wrap gap-4 items-end">
                        <div>
                            <label for="month" class="block text-sm font-medium text-gray-700">Filter by Month</label>
                            <input type="month" name="month" id="month" value="{{ request('month') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="tenant_id" class="block text-sm font-medium text-gray-700">Filter by Tenant</label>
                            <select name="tenant_id" id="tenant_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All Tenants</option>
                                @foreach($tenants as $tenant)
                                    <option value="{{ $tenant->id }}" {{ request('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                        {{ $tenant->name }} (Room: {{ $tenant->room_no }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700">
                                Filter
                            </button>
                            <a href="{{ route('electricity.index') }}" class="text-gray-600 ml-2 hover:underline">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full leading-normal">
                            <thead>
                                <tr>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Month
                                    </th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Tenant / Room
                                    </th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Current Unit
                                    </th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Units Used
                                    </th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Rate
                                    </th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Cost
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $invoice)
                                    @php
                                        // Calculate used units for display if not stored
                                        $unitsUsed = $electricRate > 0 ? round($invoice->electricity_charge / $electricRate) : 0;
                                    @endphp
                                    <tr>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            {{ \Carbon\Carbon::parse($invoice->month)->format('M Y') }}
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <div class="flex items-center">
                                                <div class="ml-3">
                                                    <p class="text-gray-900 whitespace-no-wrap">
                                                        {{ $invoice->tenant->name ?? 'N/A' }}
                                                    </p>
                                                    <p class="text-gray-600 text-xs">
                                                        Room: {{ $invoice->tenant->room_no ?? 'N/A' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right">
                                            {{ $invoice->electricity_units }}
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right font-bold">
                                            {{ $unitsUsed }}
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right">
                                            ₹{{ $electricRate }}
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right">
                                            ₹{{ number_format($invoice->electricity_charge, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                                            No electricity records found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $invoices->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
