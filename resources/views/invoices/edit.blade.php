<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Invoice') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto">
            <div class="bg-white p-6 rounded shadow">
                <form action="{{ route('invoices.update', $invoice->id) }}" method="POST" id="invoice-form">
                    @csrf
                    @method('PUT')

                    <!-- Tenant -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Tenant</label>
                        <select name="tenant_id" class="w-full border rounded px-3 py-2" required>
                            <option value="">Select Tenant</option>
                            @foreach ($tenants as $tenant)
                                <option value="{{ $tenant->id }}" {{ $tenant->id == $invoice->tenant_id ? 'selected' : '' }}>
                                    {{ $tenant->name }} (Room: {{ $tenant->room_no }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Month -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Month</label>
                        <input type="month" name="month" value="{{ \Carbon\Carbon::parse($invoice->month)->format('Y-m') }}"
                            class="w-full border rounded px-3 py-2" required>
                    </div>

                    <!-- Electricity Units -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Electricity Units</label>
                        <input type="number" name="electricity_units" id="electricity_units"
                            value="{{ $invoice->electricity_units }}" step="0.01"
                            class="w-full border rounded px-3 py-2" required>
                    </div>

                    <!-- Electricity Charge (readonly) -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Electricity Charge</label>
                        <input type="number" name="electricity_charge" id="electricity_charge"
                            value="{{ $invoice->electricity_charge }}" step="0.01" class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
                    </div>

                    <!-- Water Charge -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Water Charge</label>
                        <input type="number" name="water_charge" id="water_charge"
                            value="{{ $invoice->water_charge }}" step="0.01" class="w-full border rounded px-3 py-2" required>
                    </div>

                    <!-- Total Amount (readonly) -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Total Amount</label>
                        <input type="number" name="total_amount" id="total_amount"
                            value="{{ $invoice->total_amount }}" step="0.01" class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
                    </div>

                    <!-- Status -->
                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700">Status</label>
                        <select name="status" class="w-full border rounded px-3 py-2">
                            <option value="unpaid" {{ $invoice->status == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                            <option value="paid" {{ $invoice->status == 'paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                            Update Invoice
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const electricityRate = 10; // change if needed
        const unitInput = document.getElementById('electricity_units');
        const waterInput = document.getElementById('water_charge');
        const electricityChargeInput = document.getElementById('electricity_charge');
        const totalAmountInput = document.getElementById('total_amount');

        function calculateCharges() {
            const units = parseFloat(unitInput.value) || 0;
            const water = parseFloat(waterInput.value) || 0;
            const elecCharge = units * electricityRate;

            electricityChargeInput.value = elecCharge.toFixed(2);
            totalAmountInput.value = (elecCharge + water).toFixed(2);
        }

        unitInput.addEventListener('input', calculateCharges);
        waterInput.addEventListener('input', calculateCharges);
        window.addEventListener('load', calculateCharges);
    </script>
</x-app-layout>
