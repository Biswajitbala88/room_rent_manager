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
                        <select name="tenant_id" id="tenant_id" class="w-full border rounded px-3 py-2" required>
                            <option value="">Select Tenant</option>
                            @foreach ($tenants as $tenant)
                                <option 
                                    value="{{ $tenant->id }}" 
                                    {{ $tenant->id == $invoice->tenant_id ? 'selected' : '' }}
                                    data-rent="{{ $tenant->rent_amount }}"
                                    data-is-water-charge="{{ $tenant->is_water_charge }}"
                                >
                                    {{ $tenant->name }} (Room: {{ $tenant->room_no }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Month -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Month</label>
                        <input type="month" name="month" id="month"
                            value="{{ \Carbon\Carbon::parse($invoice->month)->format('Y-m') }}"
                            class="w-full border rounded px-3 py-2" required>
                    </div>

                    <!-- Electricity Units -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Electricity Units</label>
                        <input type="number" name="electricity_units" id="electricity_units"
                            value="{{ $invoice->electricity_units }}" step="0.01"
                            class="w-full border rounded px-3 py-2" required>
                    </div>

                    <!-- Last Month Units (readonly) -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Last Month Units</label>
                        <input type="number" id="last_month_units" class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
                    </div>

                    <!-- Unit Diff (readonly) -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Unit Difference</label>
                        <input type="number" id="unit_diff" class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
                    </div>

                    <!-- Electricity Charge (readonly) -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Electricity Charge</label>
                        <input type="number" name="electricity_charge" id="electricity_charge"
                            value="{{ $invoice->electricity_charge }}" step="0.01"
                            class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
                    </div>

                    <!-- Water Charge -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Water Charge</label>
                        <input type="number" name="water_charge" id="water_charge"
                            value="{{ $invoice->water_charge }}" step="0.01"
                            class="w-full border rounded px-3 py-2" required>
                    </div>

                    <!-- Total Amount (readonly) -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Total Amount</label>
                        <input type="number" name="total_amount" id="total_amount"
                            value="{{ $invoice->total_amount }}" step="0.01"
                            class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
                    </div>

                    <!-- Status -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Received Amount</label>
                        <input type="number" name="received_amount" id="received_amount"
                            value="{{ $invoice->received_amount }}" step="0.01"
                            class="w-full border rounded px-3 py-2 " >
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

    @php
        $electricRate = config('constants.electric_rate');
    @endphp

    <script>
        const electricityRate = {{ $electricRate }};
        const tenantSelect = document.getElementById('tenant_id');
        const monthInput = document.getElementById('month');
        const unitInput = document.getElementById('electricity_units');
        const lastUnitInput = document.getElementById('last_month_units');
        const diffInput = document.getElementById('unit_diff');
        const chargeInput = document.getElementById('electricity_charge');
        const waterInput = document.getElementById('water_charge');
        const totalInput = document.getElementById('total_amount');

        function toggleWaterChargeField() {
            const isWaterCharge = tenantSelect.selectedOptions[0]?.dataset.isWaterCharge;
            if (isWaterCharge == "0") {
                waterInput.readOnly = true;
                waterInput.value = 0;
            } else {
                waterInput.readOnly = false;
            }
        }


        function fetchLastMonthUnits() {
            const tenantId = tenantSelect.value;
            const month = monthInput.value;

            if (!tenantId || !month) return;

            fetch(`/tenant-last-units/${tenantId}/${month}`)
                .then(res => res.json())
                .then(data => {
                    const lastUnits = parseFloat(data.last_units) || 0;
                    lastUnitInput.value = lastUnits;
                    calculateCharges();
                });
        }

        function calculateCharges() {
            const currentUnits = parseFloat(unitInput.value) || 0;
            const lastUnits = parseFloat(lastUnitInput.value) || 0;
            const waterCharge = parseFloat(waterInput.value) || 0;
            const rent = parseFloat(tenantSelect.selectedOptions[0]?.dataset.rent || 0);

            const unitDiff = Math.max(currentUnits - lastUnits, 0);
            const electricityCharge = unitDiff * electricityRate;
            const total = electricityCharge + waterCharge + rent;

            diffInput.value = unitDiff.toFixed(2);
            chargeInput.value = electricityCharge.toFixed(2);
            totalInput.value = total.toFixed(2);
        }

        unitInput.addEventListener('input', calculateCharges);
        waterInput.addEventListener('input', calculateCharges);
        tenantSelect.addEventListener('change', () => {
            fetchLastMonthUnits();
            toggleWaterChargeField();
        });
        monthInput.addEventListener('change', fetchLastMonthUnits);

        window.addEventListener('load', () => {
            fetchLastMonthUnits();
            toggleWaterChargeField(); // initial state
        });
    </script>
</x-app-layout>
