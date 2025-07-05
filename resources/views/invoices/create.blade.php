<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Invoice') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto">
            <div class="bg-white p-6 rounded shadow">
                <form id="invoice-form" action="{{ route('invoices.store') }}" method="POST">
                    @csrf

                    <!-- Tenant -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Tenant</label>
                        <select name="tenant_id" id="tenant_id" required class="w-full border rounded px-3 py-2">
                            <option value="">Select Tenant</option>
                            @foreach ($tenants as $tenant)
                                <option value="{{ $tenant->id }}" data-rent="{{ $tenant->rent_amount }}">{{ $tenant->name }} (Room: {{ $tenant->room_no }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Month -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Month</label>
                        <input type="month" name="month" required class="w-full border rounded px-3 py-2">
                    </div>

                    <!-- Electricity Units -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Electricity Units</label>
                        <input type="number" id="electricity_units" name="electricity_units" step="0.01" required class="w-full border rounded px-3 py-2">
                    </div>

                    <!-- Electricity Charge -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Electricity Charge (₹)</label>
                        <input type="number" id="electricity_charge" name="electricity_charge" step="0.01" readonly class="w-full border rounded px-3 py-2 bg-gray-100">
                    </div>

                    <!-- Water Charge -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Water Charge (₹)</label>
                        <input type="number" id="water_charge" name="water_charge" step="0.01" required class="w-full border rounded px-3 py-2">
                    </div>

                    <!-- Status -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Status</label>
                        <select name="status" required class="w-full border rounded px-3 py-2">
                            <option value="unpaid">Unpaid</option>
                            <option value="paid">Paid</option>
                        </select>
                    </div>

                    <!-- Total Amount (Display Only) -->
                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700">Total Amount (₹)</label>
                        <input type="text" id="total_amount_display" readonly class="w-full border rounded px-3 py-2 bg-gray-100 font-semibold text-lg">
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

    <!-- JavaScript -->
    <script>
        const tenantSelect = document.getElementById('tenant_id');
        const electricityUnitsInput = document.getElementById('electricity_units');
        const electricityChargeInput = document.getElementById('electricity_charge');
        const waterChargeInput = document.getElementById('water_charge');
        const totalAmountDisplay = document.getElementById('total_amount_display');

        let rentAmount = 0;

        function calculateCharges() {
            const units = parseFloat(electricityUnitsInput.value) || 0;
            const water = parseFloat(waterChargeInput.value) || 0;
            const electricityCharge = units * 7; // ₹7/unit

            electricityChargeInput.value = electricityCharge.toFixed(2);

            const total = rentAmount + electricityCharge + water;
            totalAmountDisplay.value = total.toFixed(2);
        }

        tenantSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            rentAmount = parseFloat(selectedOption.getAttribute('data-rent')) || 0;
            calculateCharges();
        });

        electricityUnitsInput.addEventListener('input', calculateCharges);
        waterChargeInput.addEventListener('input', calculateCharges);
    </script>
</x-app-layout>
