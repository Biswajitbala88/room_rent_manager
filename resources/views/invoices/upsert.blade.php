<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($invoice) ? __('Edit Invoice') : __('Create Invoice') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto">
            <div class="bg-white p-6 rounded shadow">
                <form 
                    action="{{ isset($invoice) ? route('invoices.update', $invoice->id) : route('invoices.store') }}" 
                    method="POST" 
                    id="invoice-form"
                >
                    @csrf
                    @if(isset($invoice))
                        @method('PUT')
                    @endif

                    <!-- Tenant -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Tenant</label>
                        <select name="tenant_id" id="tenant_id" required class="w-full border rounded px-3 py-2">
                            <option value="">Select Tenant</option>
                            @foreach ($tenants as $tenant)
                                <option 
                                    value="{{ $tenant->id }}" 
                                    {{ (isset($invoice) && $invoice->tenant_id == $tenant->id) ? 'selected' : '' }}
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
                        <input 
                            type="month" 
                            name="month" 
                            id="month" 
                            required 
                            class="w-full border rounded px-3 py-2" 
                            value="{{ isset($invoice) ? \Carbon\Carbon::parse($invoice->month)->format('Y-m') : now()->format('Y-m') }}"
                        >
                    </div>

                    <!-- Electricity Units -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Electricity Units (Current)</label>
                        <input 
                            type="number" 
                            id="electricity_units" 
                            name="electricity_units" 
                            step="1" 
                            required 
                            class="w-full border rounded px-3 py-2"
                            value="{{ $invoice->electricity_units ?? '' }}"
                        >
                    </div>

                    <!-- Last Month Units (Display/Hidden) -->
                    <div class="mb-4">
                         <label class="block font-medium text-sm text-gray-700">Last Month Units</label>
                        <input type="number" id="last_electric_unit" name="last_electric_unit" class="w-full border rounded px-3 py-2 bg-gray-100" >
                    </div>

                    <!-- Unit Difference -->
                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700">Unit Difference</label>
                        <input type="text" id="unit_diff_display" class="w-full border rounded px-3 py-2 bg-gray-100 font-semibold text-lg">
                    </div>

                    <!-- Electricity Charge -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Electricity Charge (₹)</label>
                        <input 
                            type="number" 
                            id="electricity_charge" 
                            name="electricity_charge" 
                            step="1" 
                            class="w-full border rounded px-3 py-2 bg-gray-100"
                            value="{{ $invoice->electricity_charge ?? '' }}"
                        >
                    </div>

                    <!-- Water Charge -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Water Charge (₹)</label>
                        <input 
                            type="number" 
                            id="water_charge" 
                            name="water_charge" 
                            step="0.01" 
                            required 
                            class="w-full border rounded px-3 py-2 bg-gray-100" 
                            readonly
                            value="{{ $invoice->water_charge ?? '' }}"
                        >
                    </div>

                    <!-- Total Amount -->
                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700">Total Amount (₹)</label>
                        <input 
                            type="number" 
                            name="total_amount"
                            id="total_amount_display" 
                            readonly 
                            class="w-full border rounded px-3 py-2 bg-gray-100 font-semibold text-lg"
                            value="{{ $invoice->total_amount ?? '' }}"
                        >
                    </div>

                    <!-- Received Amount -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Received Amount</label>
                        <input 
                            type="number" 
                            name="received_amount" 
                            id="received_amount"
                            value="{{ old('received_amount', $invoice->received_amount ?? '') }}" 
                            step="0.01" 
                            class="w-full border rounded px-3 py-2"
                        >
                    </div>

                    <div class="mb-4 grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Payment Mode</label>
                            <select name="payment_mode" id="payment_mode" class="w-full border rounded px-3 py-2">
                                <option value="Cash">Cash</option>
                                <option value="UPI">UPI</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Payment Date</label>
                            <input type="date" name="payment_date" id="payment_date" class="w-full border rounded px-3 py-2" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    @if(isset($invoice))
                    <!-- Exclude Invoice Checkbox -->
                    <div class="mb-4 flex items-center">
                        <input type="hidden" name="is_excluded" value="0">
                        <input 
                            type="checkbox" 
                            name="is_excluded" 
                            id="is_excluded" 
                            value="1" 
                            {{ (isset($invoice) && $invoice->is_excluded) ? 'checked' : '' }} 
                            class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        >
                        <label for="is_excluded" class="text-sm font-medium text-gray-700">Exclude from Due Payments</label>
                    </div>
                    @else
                    <!-- Closer Checkbox (Create only) -->
                     @if (request()->segment(2) === 'closer')
                        <div class="mb-4">
                            Is closer? 
                            <input class="ms-3" type="checkbox" name="closer" value="1" checked>
                        </div>
                    @endif
                    @endif

                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            {{ isset($invoice) ? 'Update Invoice' : 'Create Invoice' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    @php
        $electricRate = config('constants.ELECTRIC_RATE', 10);
    @endphp

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            const electricRate = {{ $electricRate }};
            const $tenantSelect = $('#tenant_id');
            const $monthInput = $('#month');
            const $electricityUnitsInput = $('#electricity_units');
            const $electricityChargeInput = $('#electricity_charge');
            const $waterChargeInput = $('#water_charge');
            const $totalAmountDisplay = $('#total_amount_display');
            const $lastUnitInput = $('#last_electric_unit');
            const $unitDiffDisplay = $('#unit_diff_display');
            
            let rentAmount = 0;
            let lastUnit = 0;
            let water = 0;
            let isWaterCharge = 0;

            function calculateCharges(isInitialLoad = false) {
                isInitialLoad = isInitialLoad === true;
                const $selectedOption = $tenantSelect.find('option:selected');
                const startMonth = $selectedOption.data('start-month') || '';
                const invoiceMonth = $monthInput.val();  
                const startMonthFormatted = startMonth.slice(0, 7);
                
                const currentUnits = parseFloat($electricityUnitsInput.val()) || 0;
                
                // If it's the initial load, respect the DB value for last unit.
                if (!isInitialLoad) {
                    lastUnit = parseFloat($lastUnitInput.val()) || 0;
                }

                let electricityCharge = 0;

                if (startMonthFormatted === invoiceMonth) {
                    // Same month → no electricity charge
                    $electricityChargeInput.val("0.00");
                    $unitDiffDisplay.val("0");
                    
                    if (!isInitialLoad) {
                        $lastUnitInput.val(currentUnits);
                    } else if ($lastUnitInput.val() === '' || parseFloat($lastUnitInput.val()) === 0) {
                        $lastUnitInput.val(currentUnits);
                    }
                } else {
                    // Different month → calculate electricity
                    if (!isInitialLoad) {
                        const unitDiff = Math.max(currentUnits - lastUnit, 0);
                        $unitDiffDisplay.val(unitDiff);
    
                        electricityCharge = unitDiff * electricRate;
                        $electricityChargeInput.val(electricityCharge.toFixed(2));
                    } else {
                        // On initial load, preserve DB unit diff and charge
                        const unitDiff = Math.max(currentUnits - parseFloat($lastUnitInput.val() || 0), 0);
                        $unitDiffDisplay.val(unitDiff);
                        electricityCharge = parseFloat($electricityChargeInput.val()) || 0;
                    }
                }

                water = parseFloat($waterChargeInput.val()) || 0;
                
                if (!isInitialLoad) {
                    const total = rentAmount + electricityCharge + water;
                    $totalAmountDisplay.val(total.toFixed(2));
                }
            }

            function fetchLastUnits(isInitialLoad = false) {
                const tenantId = $tenantSelect.val();
                const month = $monthInput.val();
                if (!tenantId || !month) return;

                if (isInitialLoad && window.location.href.includes('/edit')) {
                    // We don't fetch last units on initial load of edit page, 
                    // because we already have the correct last unit saved in DB for this invoice!
                    calculateCharges(true);
                    return;
                }

                $.ajax({
                    url: `/tenant-last-units/${tenantId}/${month}`,
                    method: 'GET',
                    success: function(data) {
                        lastUnit = parseFloat(data.last_units) || 0;
                        
                        const $selectedOption = $tenantSelect.find('option:selected');
                        const startMonth = $selectedOption.data('start-month') || '';
                        const startMonthFormatted = startMonth.slice(0, 7);

                        if (startMonthFormatted !== month) {
                            $lastUnitInput.val(lastUnit);
                        }
                        
                        calculateCharges(false);
                    }
                });
            }

            function updateWaterCharge(isInitialLoad = false) {
                const $selectedOption = $tenantSelect.find('option:selected');
                rentAmount = parseFloat($selectedOption.data('rent')) || 0;
                isWaterCharge = parseInt($selectedOption.data('is-water-charge')) || 0;
                const waterChargeVal = parseFloat($selectedOption.data('water-charge')) || 0;

                const startMonth = $selectedOption.data('start-month') || '';
                const invoiceMonth = $monthInput.val();  
                const startMonthFormatted = startMonth.slice(0, 7);

                if (startMonthFormatted === invoiceMonth) {
                    water = 0;
                    $waterChargeInput.val('0.00');
                    $waterChargeInput.addClass('bg-gray-100').prop('readonly', true);
                } else if (isWaterCharge === 1) {
                    water = waterChargeVal;
                    // On initial edit load, if the invoice already has a water charge, keep it.
                    // Otherwise, set it from the tenant's default water charge.
                    if (!isInitialLoad) {
                        $waterChargeInput.val(water.toFixed(2));
                    } else if ($waterChargeInput.val() === '' || parseFloat($waterChargeInput.val()) === 0) {
                        $waterChargeInput.val(water.toFixed(2));
                    }
                    $waterChargeInput.removeClass('bg-gray-100').prop('readonly', false);
                } else {
                    water = 0;
                    $waterChargeInput.val('0.00');
                    $waterChargeInput.addClass('bg-gray-100').prop('readonly', true);
                }

                if (!isInitialLoad) {
                    calculateCharges();
                } else {
                    // Just set the water variable, do not recalculate total yet to avoid overwriting DB state early
                    water = parseFloat($waterChargeInput.val()) || 0;
                }
            }
            
            // Event Listeners
            $tenantSelect.on('change', function () {
                $waterChargeInput.val(''); 
                updateWaterCharge();
                fetchLastUnits();
            });

            $monthInput.on('change', function() {
                updateWaterCharge();
                fetchLastUnits();
            });

            $electricityUnitsInput.on('input', function() { calculateCharges(); });
            $waterChargeInput.on('input', function () {
                water = parseFloat($(this).val()) || 0;
                calculateCharges();
            });
            
            // Initial Load Logic
            if ($tenantSelect.val()) {
                const $selectedOption = $tenantSelect.find('option:selected');
                rentAmount = parseFloat($selectedOption.data('rent')) || 0;
                
                // Always call these on load to populate water charge correctly since it's not stored in invoices table
                updateWaterCharge(true);
                fetchLastUnits(true);
            }
        });
    </script>
</x-app-layout>
