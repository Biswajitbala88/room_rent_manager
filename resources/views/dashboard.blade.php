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
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Filter with month</label>
                        <input type="month" name="filter_month" id="filter_month" required class="w-full border rounded px-3 py-2">
                    </div>
                    <!-- Summary Counter Boxes -->
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
                            <tbody id="invoice_body">
                                <!-- JS will inject rows -->
                            </tbody>
                        </table>
                    </div>


                </div>
            </div>
        </div>
    </div>


<script>
document.getElementById('tenant_select').addEventListener('change', function () {
    let tenantId = this.value;

    if (tenantId === "") {
        document.getElementById('due_invoices_table').classList.add('hidden');
        return;
    }

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

                document.getElementById('due_invoices_table').classList.remove('hidden');
            } else {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center p-4">No due invoices found.</td></tr>';
                document.getElementById('due_invoices_table').classList.remove('hidden');
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
            // Update table values
            document.getElementById(`received_${invoiceId}`).innerText = data.new_received;
            document.getElementById(`due_${invoiceId}`).innerText = data.new_due;
            document.getElementById(`payment_${invoiceId}`).value = '';
        } else {
            alert(data.message || "Error updating payment.");
        }
    })
    .catch(err => console.error(err));
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

document.addEventListener('DOMContentLoaded', function () {
    // Set default month to current
    const today = new Date();
    const currentMonth = today.toISOString().slice(0, 7); // format: "YYYY-MM"
    document.getElementById('filter_month').value = currentMonth;

    // Initial summary load
    loadSummary(currentMonth);
});

document.getElementById('filter_month').addEventListener('change', function () {
    loadSummary(this.value);
});

</script>



</x-app-layout>
