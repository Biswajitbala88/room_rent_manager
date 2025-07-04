<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Invoice') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto">
            <div class="bg-white p-6 rounded shadow">
                <form action="{{ route('invoices.store') }}" method="POST">
                    @csrf

                    <!-- Tenant -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Tenant</label>
                        <select name="tenant_id" class="fshadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">Select Tenant</option>
                            @foreach ($tenants as $tenant)
                                <option value="{{ $tenant->id }}">{{ $tenant->name }} (Room: {{ $tenant->room_no }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Month -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Month</label>
                        <input type="month" name="month" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>

                    <!-- Electricity Units -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Electricity Units</label>
                        <input type="number" name="electricity_units" step="0.01" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <!-- Electricity Charge -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Electricity Charge</label>
                        <input type="number" name="electricity_charge" step="0.01" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <!-- Water Charge -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Water Charge</label>
                        <input type="number" name="water_charge" step="0.01" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <!-- Total Amount -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Total Amount</label>
                        <input type="number" name="total_amount" step="0.01" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <!-- Status -->
                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700">Status</label>
                        <select name="status" class="fshadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="unpaid">Unpaid</option>
                            <option value="paid">Paid</option>
                        </select>
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Create Invoice
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
