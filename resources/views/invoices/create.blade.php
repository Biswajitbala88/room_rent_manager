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
                                <option 
                                    value="{{ $tenant->id }}" 
                                    data-rent="{{ $tenant->rent_amount }}" 
                                    data-start-month="{{ $tenant->start_date }}" 
                                    data-is-water-charge="{{ $tenant->is_water_charge }}"
                                    data-water-charge="{{ $tenant->water_charge }}"
                                >
                                    {{ $tenant->name }} (Room: {{ $tenant->room_no }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Month -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Month</label>
                        <input type="month" name="month" id="month" required class="w-full border rounded px-3 py-2" value="{{ now()->format('Y-m') }}">
                    </div>

                    <!-- Electricity Units -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Electricity Units (Current)</label>
                        <input type="number" id="electricity_units" name="electricity_units" step="1" required class="w-full border rounded px-3 py-2">
                    </div>

                    <!-- Hidden Input to Store Last Units -->
                    <input type="hidden" id="last_electric_unit" name="last_electric_unit">

                    <!-- Unit Difference -->
                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700">Unit Difference</label>
                        <input type="text" id="unit_diff_display" readonly class="w-full border rounded px-3 py-2 bg-gray-100 font-semibold text-lg">
                    </div>

                    <!-- Electricity Charge -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Electricity Charge (₹)</label>
                        <input type="number" id="electricity_charge" name="electricity_charge" step="1" readonly class="w-full border rounded px-3 py-2 bg-gray-100">
                    </div>

                    <!-- Water Charge -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Water Charge (₹)</label>
                        <input type="number" id="water_charge" name="water_charge" step="0.01" required class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
                    </div>

                    <!-- Total Amount -->
                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700">Total Amount (₹)</label>
                        <input type="text" id="total_amount_display" readonly class="w-full border rounded px-3 py-2 bg-gray-100 font-semibold text-lg">
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Create Invoice
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
        const tenantSelect = document.getElementById('tenant_id');
        const monthInput = document.getElementById('month');
        const electricityUnitsInput = document.getElementById('electricity_units');
        const electricityChargeInput = document.getElementById('electricity_charge');
        const waterChargeInput = document.getElementById('water_charge');
        const totalAmountDisplay = document.getElementById('total_amount_display');
        const lastUnitInput = document.getElementById('last_electric_unit');
        const unitDiffDisplay = document.getElementById('unit_diff_display');

        const electricRate = {{ $electricRate }};
        let rentAmount = 0;
        let lastUnit = 0;
        let water = 0;

        function calculateCharges() {
            const selectedOption = tenantSelect.options[tenantSelect.selectedIndex];
            const startMonth = selectedOption.dataset.startMonth;
            const invoiceMonth = monthInput.value;  
            const startMonthFormatted = startMonth.slice(0, 7);
            
            const currentUnits = parseFloat(electricityUnitsInput.value) || 0;
            const unitDiff = Math.max(currentUnits - lastUnit, 0);
            unitDiffDisplay.value = unitDiff;

            if (startMonthFormatted === invoiceMonth) {
                // Same month → only rent + water, no electricity charge
                electricityChargeInput.value = "0.00";
                const total = rentAmount + water;
                totalAmountDisplay.value = total.toFixed(2);
            } else {
                // Different month → rent + water + electricity
                const electricityCharge = unitDiff * electricRate;
                electricityChargeInput.value = electricityCharge.toFixed(2);

                const total = rentAmount + electricityCharge + water;
                totalAmountDisplay.value = total.toFixed(2);
            }
        }

        function fetchLastUnits() {
            const tenantId = tenantSelect.value;
            const month = monthInput.value;
            if (!tenantId || !month) return;

            fetch(`/tenant-last-units/${tenantId}/${month}`)
                .then(res => res.json())
                .then(data => {
                    lastUnit = parseFloat(data.last_units) || 0;
                    lastUnitInput.value = lastUnit;
                    calculateCharges();
                });
        }

        function updateWaterCharge() {
            const selectedOption = tenantSelect.options[tenantSelect.selectedIndex];
            rentAmount = parseFloat(selectedOption.getAttribute('data-rent')) || 0;

            const isWaterCharge = parseInt(selectedOption.getAttribute('data-is-water-charge')) || 0;
            const waterCharge = parseFloat(selectedOption.getAttribute('data-water-charge')) || 0;

            if (isWaterCharge === 1) {
                water = waterCharge;
                waterChargeInput.value = waterCharge.toFixed(2);
                // waterChargeInput.classList.remove('bg-gray-100');
                // waterChargeInput.removeAttribute('readonly');
            } else {
                water = 0;
                waterChargeInput.value = '0.00';
                waterChargeInput.classList.add('bg-gray-100');
                waterChargeInput.setAttribute('readonly', true);
            }

            calculateCharges();
        }

        tenantSelect.addEventListener('change', function () {
            updateWaterCharge();
            fetchLastUnits();
        });

        monthInput.addEventListener('change', fetchLastUnits);
        electricityUnitsInput.addEventListener('input', calculateCharges);
        waterChargeInput.addEventListener('input', function () {
            water = parseFloat(this.value) || 0;
            calculateCharges();
        });
    </script>
</x-app-layout>
