<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($invoice) ? __('Edit Invoice') : __('Add Invoice') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto">
            <div class="bg-white p-6 rounded shadow">
                <form 
                    action="{{ isset($invoice) ? route('invoices.update', $invoice->id) : route('invoices.store') }}" 
                    method="POST" id="invoice-form"
                >
                    @csrf
                    @if(isset($invoice))
                        @method('PUT')
                    @endif

                    <!-- Tenant -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Tenant</label>
                        <select name="tenant_id" id="tenant_id" class="w-full border rounded px-3 py-2" required>
                            <option value="">Select Tenant</option>
                            @foreach ($tenants as $tenant)
                                <option 
                                    value="{{ $tenant->id }}" 
                                    {{ (isset($invoice) && $tenant->id == $invoice->tenant_id) ? 'selected' : '' }}
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
                        <input type="month" name="month" id="month"
                            value="{{ isset($invoice) ? \Carbon\Carbon::parse($invoice->month)->format('Y-m') : '' }}"
                            class="w-full border rounded px-3 py-2" required>
                    </div>

                    <!-- Electricity Units -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Electricity Units</label>
                        <input type="number" name="electricity_units" id="electricity_units"
                            value="{{ $invoice->electricity_units ?? '' }}" step="1"
                            class="w-full border rounded px-3 py-2" required>
                    </div>

                    <!-- Last Month Units -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Last Month Units</label>
                        <input type="number" id="last_month_units" class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
                    </div>

                    <!-- Unit Diff -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Unit Difference</label>
                        <input type="number" id="unit_diff" class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
                    </div>

                    <!-- Electricity Charge -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Electricity Charge</label>
                        <input type="number" name="electricity_charge" id="electricity_charge"
                            value="{{ $invoice->electricity_charge ?? '' }}" step="1"
                            class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
                    </div>

                    <!-- Water Charge -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Water Charge</label>
                        <input type="number" name="water_charge" id="water_charge"
                            value="{{ $invoice->water_charge ?? '' }}" step="1"
                            class="w-full border rounded px-3 py-2 bg-gray-100" required readonly>
                    </div>

                    <!-- Total Amount -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Total Amount</label>
                        <input type="number" name="total_amount" id="total_amount"
                            value="{{ $invoice->total_amount ?? '' }}" step="1"
                            class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
                    </div>

                    <!-- Received Amount -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Received Amount</label>
                        <input type="number" name="received_amount" id="received_amount"
                            value="{{ $invoice->received_amount ?? '' }}" step="1"
                            class="w-full border rounded px-3 py-2">
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                            {{ isset($invoice) ? 'Update Invoice' : 'Save Invoice' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @php
        $electricRate = config('constants.electric_rate');
    @endphp

    <!-- jQuery -->
    <script>
        $(function() {
            const electricRate = {{ $electricRate }};

            function toggleWaterChargeField() {
                const $selected = $('#tenant_id option:selected');
                const isWaterCharge = $selected.data('is-water-charge');
                const waterCharge = parseFloat($selected.data('water-charge') || 0);

                if (isWaterCharge == 0) {
                    $('#water_charge').val(0).prop('readonly', true).addClass('bg-gray-100');
                } else {
                    if (!$('#water_charge').val() || parseFloat($('#water_charge').val()) === 0) {
                        $('#water_charge').val(waterCharge.toFixed(2)).prop('readonly', true);
                    }
                }
            }

            function fetchLastMonthUnits() {
                const tenantId = $('#tenant_id').val();
                const month = $('#month').val();
                if (!tenantId || !month) return;

                $.getJSON(`/tenant-last-units/${tenantId}/${month}`, function(data) {
                    const lastUnits = parseFloat(data.last_units || 0);
                    $('#last_month_units').val(lastUnits);
                    calculateCharges();
                });
            }

            function calculateCharges() {
                const $selected = $('#tenant_id option:selected');
                const startMonth = $selected.data('start-month').slice(0,7);
                const invoiceMonth = $('#month').val();

                const currentUnits = parseFloat($('#electricity_units').val() || 0);
                const lastUnits = parseFloat($('#last_month_units').val() || 0);
                const waterCharge = parseFloat($('#water_charge').val() || 0);
                const rent = parseFloat($selected.data('rent') || 0);

                const unitDiff = Math.max(currentUnits - lastUnits, 0);

                if (startMonth === invoiceMonth) {
                    $('#unit_diff').val(unitDiff.toFixed(2));
                    $('#electricity_charge').val("0.00");
                    $('#total_amount').val((rent + waterCharge).toFixed(2));
                } else {
                    const electricityCharge = unitDiff * electricRate;
                    $('#unit_diff').val(unitDiff.toFixed(2));
                    $('#electricity_charge').val(electricityCharge.toFixed(2));
                    $('#total_amount').val((rent + waterCharge + electricityCharge).toFixed(2));
                }
            }

            // Event listeners
            $('#tenant_id').on('change', function() {
                fetchLastMonthUnits();
                toggleWaterChargeField();
            });

            $('#month').on('change', fetchLastMonthUnits);
            $('#electricity_units, #water_charge').on('input', calculateCharges);

            // Initial load
            fetchLastMonthUnits();
            toggleWaterChargeField();
            calculateCharges();
        });
    </script>
</x-app-layout>
