<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-800">
                {{ __('Dashboard') }}
            </h2>

            <button id="openModalBtn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium">
                Create Invoice
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Filter with month</label>
                        <input type="month" name="filter_month" id="filter_month" required class="w-full border rounded px-3 py-2">
                    </div>

                    <!-- Summary Counters -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4">
                            <div class="text-sm font-bold">Total Pending Invoices</div>
                            <div class="text-2xl font-semibold pending-count">{{ $totalPendingInvoices ? count($totalPendingInvoices) : 0 }}</div>
                        </div>
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
                            <div class="text-sm font-bold">Total Due Amount (₹)</div>
                            <div class="text-2xl font-semibold due-amount">₹{{ $totalDueAmount ? number_format($totalDueAmount, 2) : 0 }}</div>
                        </div>
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4">
                            <div class="text-sm font-bold">Total Received Amount (₹)</div>
                            <div class="text-2xl font-semibold received-amount">₹{{ $totalReceivedAmount ? number_format($totalReceivedAmount, 2) : 0 }}</div>
                        </div>
                    </div>

                    <!-- Dropdown -->
                    <div class="mb-4">
                        <label for="tenant_select" class="block text-gray-700 font-bold mb-2">Due Payments</label>
                        <select id="tenant_select" class="w-full border rounded px-3 py-2">
                            <option value="">Select Tenant</option>
                            @foreach ($tenants as $tenant)
                                <option value="{{ $tenant->id }}">{{ $tenant->name }} (Room: {{ $tenant->room_no }}) Total Due Invoice: {{$tenant->due_invoice_count}}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Table for due invoices -->
                    <div id="due_invoices_section">
                        <table class="table-auto w-full border-collapse border border-gray-300 mt-4 hidden" id="due_invoices_table">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border px-4 py-2">Invoice ID</th>
                                    <th class="border px-4 py-2">Date</th>
                                    <th class="border px-4 py-2">Total</th>
                                    <th class="border px-4 py-2">Received</th>
                                    <th class="border px-4 py-2">Due</th>
                                    <th class="border px-4 py-2">Add Payment</th>
                                </tr>
                            </thead>
                            <tbody id="invoice_body"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Backdrop -->
    <div id="invoiceModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <!-- Modal Content -->
        <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg relative">
            <!-- Close Button -->
            <button onclick="document.getElementById('invoiceModal').classList.add('hidden')" class="absolute top-2 right-2 text-gray-600 hover:text-black text-2xl">&times;</button>

            <h3 class="text-lg font-semibold mb-4">Create Invoice</h3>
            <form id="invoice-form" action="{{ route('invoices.store') }}" method="POST">
                @csrf

                <!-- Tenant -->
                <div class="mb-4">
                    <label class="block font-medium text-sm text-gray-700">Tenant</label>
                    <select name="tenant_id" id="tenant_id" required class="w-full border rounded px-3 py-2">
                        <option value="">Select Tenant</option>
                        @foreach ($tenants as $tenant)
                            <option value="{{ $tenant->id }}" data-rent="{{ $tenant->rent_amount }}" data-is-water-charge="{{ $tenant->is_water_charge }}" data-water-charge="{{ $tenant->water_charge }}">
                                {{ $tenant->name }} (Room: {{ $tenant->room_no }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Month -->
                <div class="mb-4">
                    <label class="block font-medium text-sm text-gray-700">Month</label>
                    <input type="month" name="month" id="month" required class="w-full border rounded px-3 py-2">
                </div>

                <!-- Electricity -->
                <div class="mb-4">
                    <label class="block font-medium text-sm text-gray-700">Electricity Units (Current)</label>
                    <input type="number" id="electricity_units" name="electricity_units" step="0.01" required class="w-full border rounded px-3 py-2">
                </div>
                <input type="hidden" id="last_electric_unit" name="last_electric_unit">
                <div class="mb-4">
                    <label class="block font-medium text-sm text-gray-700">Unit Difference</label>
                    <input type="text" id="unit_diff_display" readonly class="w-full border rounded px-3 py-2 bg-gray-100 font-semibold text-lg">
                </div>

                <div class="mb-4">
                    <label class="block font-medium text-sm text-gray-700">Electricity Charge (₹)</label>
                    <input type="number" id="electricity_charge" name="electricity_charge" step="0.01" readonly class="w-full border rounded px-3 py-2 bg-gray-100">
                </div>

                <div class="mb-4">
                    <label class="block font-medium text-sm text-gray-700">Water Charge (₹)</label>
                    <input type="number" id="water_charge" name="water_charge" step="0.01" readonly class="w-full border rounded px-3 py-2 bg-gray-100">
                </div>

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

    @php $electricRate = config('constants.electric_rate'); @endphp

    <!-- Meta for CSRF -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />

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
        let rentAmount = 0, lastUnit = 0, water = 0;

        function calculateCharges() {
            const currentUnits = parseFloat(electricityUnitsInput.value) || 0;
            const unitDiff = Math.max(currentUnits - lastUnit, 0);
            unitDiffDisplay.value = unitDiff;
            const electricityCharge = unitDiff * electricRate;
            electricityChargeInput.value = electricityCharge.toFixed(2);
            const total = rentAmount + electricityCharge + water;
            totalAmountDisplay.value = total.toFixed(2);
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
            } else {
                water = 0;
                waterChargeInput.value = '0.00';
            }
            calculateCharges();
        }

        tenantSelect.addEventListener('change', () => {
            updateWaterCharge();
            fetchLastUnits();
        });

        monthInput.addEventListener('change', fetchLastUnits);
        electricityUnitsInput.addEventListener('input', calculateCharges);
        waterChargeInput.addEventListener('input', function () {
            water = parseFloat(this.value) || 0;
            calculateCharges();
        });

        document.getElementById('openModalBtn').addEventListener('click', () => {
            document.getElementById('invoiceModal').classList.remove('hidden');
        });

        document.getElementById('invoiceModal').addEventListener('click', function (e) {
            if (e.target.id === 'invoiceModal') {
                this.classList.add('hidden');
            }
        });

        document.getElementById('invoice-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: formData,
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Invoice created successfully!');
                    document.getElementById('invoiceModal').classList.add('hidden');
                    form.reset();
                    const selectedMonth = document.getElementById('filter_month').value;
                    loadSummary(selectedMonth);
                } else {
                    alert(data.message || 'Error creating invoice.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Something went wrong.');
            });
        });

        document.getElementById('tenant_select').addEventListener('change', function () {
            let tenantId = this.value;
            const table = document.getElementById('due_invoices_table');
            if (!tenantId) return table.classList.add('hidden');

            fetch(`/tenants/${tenantId}/due-invoices`)
                .then(response => response.json())
                .then(data => {
                    let tbody = document.getElementById('invoice_body');
                    tbody.innerHTML = '';

                    if (data.length > 0) {
                        data.forEach(invoice => {
                            let due = invoice.total_amount - invoice.received_amount;
                            tbody.innerHTML += `
                                <tr data-id="${invoice.id}">
                                    <td class="border px-4 py-2">${invoice.id}</td>
                                    <td class="border px-4 py-2">${invoice.month}</td>
                                    <td class="border px-4 py-2">${invoice.total_amount}</td>
                                    <td class="border px-4 py-2" id="received_${invoice.id}">${invoice.received_amount}</td>
                                    <td class="border px-4 py-2 text-red-500 font-bold" id="due_${invoice.id}">${due}</td>
                                    <td class="border px-4 py-2">
                                        <input type="number" class="border px-2 py-1 w-24" id="payment_${invoice.id}" placeholder="0">
                                        <button class="bg-green-500 text-white px-3 py-1 ml-2 rounded" onclick="submitPayment(${invoice.id})">Save</button>
                                        <a href="/invoices/${invoice.id}/download" class="text-indigo-600 hover:underline ms-4">PDF</a>
                                    </td>
                                </tr>`;
                        });
                        table.classList.remove('hidden');
                    } else {
                        tbody.innerHTML = `<tr><td colspan="6" class="text-center p-4">No due invoices found.</td></tr>`;
                        table.classList.remove('hidden');
                    }
                });
        });

        function submitPayment(invoiceId) {
            let amount = document.getElementById(`payment_${invoiceId}`).value;

            if (!amount || isNaN(amount) || amount <= 0) {
                alert("Please enter a valid amount.");
                return;
            }

            fetch(`/invoices/${invoiceId}/add-payment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ amount: parseFloat(amount) })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`received_${invoiceId}`).innerText = data.new_received;
                    document.getElementById(`due_${invoiceId}`).innerText = data.new_due;
                    document.getElementById(`payment_${invoiceId}`).value = '';
                } else {
                    alert(data.message || "Error updating payment.");
                }
            });
        }

        function loadSummary(month) {
            fetch(`/dashboard-summary?month=${month}`)
                .then(response => response.json())
                .then(data => {
                    document.querySelector('.pending-count').innerText = data.totalPendingCount;
                    document.querySelector('.due-amount').innerText = `₹${data.totalDueAmount}`;
                    document.querySelector('.received-amount').innerText = `₹${data.totalReceivedAmount}`;
                });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const today = new Date();
            const currentMonth = today.toISOString().slice(0, 7);
            document.getElementById('filter_month').value = currentMonth;
            loadSummary(currentMonth);
        });

        document.getElementById('filter_month').addEventListener('change', function () {
            loadSummary(this.value);
        });
    </script>
</x-app-layout>
