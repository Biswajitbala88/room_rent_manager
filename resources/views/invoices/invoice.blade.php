<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; text-align: left; }
    </style>
</head>
<body>
    <h2>Invoice #{{ $invoice->id }}</h2>
    <p><strong>Tenant:</strong> {{ $invoice->tenant->name }}</p>
    <p><strong>Room No:</strong> {{ $invoice->tenant->room_no }}</p>
    <p><strong>Month:</strong> {{ \Carbon\Carbon::parse($invoice->month)->format('F Y') }}</p>

    <table>
        <tr>
            <th>Description</th>
            <th>Amount</th>
        </tr>
        <tr>
            <td>Monthly Rent</td>
            <td>{{ number_format($invoice->tenant->rent_amount, 2) }}</td>
        </tr>
        <tr>
            <td>Electricity ({{ $invoice->electricity_units }} units)</td>
            <td>{{ number_format($invoice->electricity_charge, 2) }}</td>
        </tr>
        <tr>
            <td>Water Charge</td>
            <td>{{ number_format($invoice->water_charge, 2) }}</td>
        </tr>
        <tr>
            <th>Total</th>
            <th>{{ number_format($invoice->total_amount, 2) }}</th>
        </tr>
    </table>

    <p><strong>Status:</strong> {{ ucfirst($invoice->status) }}</p>
</body>
</html>
