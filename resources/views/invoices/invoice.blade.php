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
p {
    margin: 5px 0;
}
.footer {
    text-align: center;
    font-size: 13px;
    color: #666666;
}
.footer hr {
    margin: 0 0 15px;
    border: 0;
    border-bottom: 1px dashed #ccc;
}
    </style>
</head>
<body>

<?php
// echo '<pre>'; print_r($invoice->tenant); exit;


?>

    <h2 style="text-align: center;">Invoice</h2>
    <p><strong>Name:</strong> {{ $invoice->tenant->name }}</p>
    <p><strong>Room No:</strong> {{ $invoice->tenant->room_no }}</p>
    <p><strong>Month:</strong> {{ \Carbon\Carbon::parse($invoice->month)->format('F Y') }}</p>

    <table>
        <tr>
            <th>Description</th>
            <th>Amount</th>
        </tr>
        <tr>
            <td>Monthly Rent</td>
            <td>₹{{ number_format($invoice->tenant->rent_amount, 2) }}</td>
        </tr>
        @if ($invoice->currentUnit > 0)
        <tr>
            <td>
                Electricity Usage: {{ $invoice->electricity_display }} units
                <br>
                <small>
                    Charge = ({{ $invoice->electricity_display }}) × ₹{{ $invoice->electricity_rate }} = ₹{{ number_format($invoice->electricity_charge, 2) }}
                </small>
            </td>
            <td>₹{{ number_format($invoice->electricity_charge, 2) }}</td>
        </tr>
        @endif


        @if($invoice->tenant->is_water_charge == 1)
        <tr>
            <td>Water Charge</td>
            <td>₹{{ number_format($invoice->water_charge, 2) }}</td>
        </tr>
        @endif
        <tr>
            <th style="background: #2980b9; color: #fff;">Total</th>
            <th style="background: #2980b9;  color: #fff;">₹{{ number_format($invoice->total_amount, 2) }}</th>
        </tr>
        <tr>
            <th>Paid</th>
            <th style="color: #006622;">₹{{ number_format($invoice->received_amount, 2) }}</th>
        </tr>
        <tr>
            <th>Due</th>
            <th style="color: #cc0000;">₹{{ number_format($invoice->total_amount - $invoice->received_amount, 2) }}</th>
        </tr>
    </table>

    <p><strong>Status:
        @if( $invoice->total_amount == $invoice->received_amount)
            <span style="color: #006622;">
                Fully Paid
            </span>
        @else
            <span style="color: #cc0000;">
                Due
            </span>
        @endif
        </strong> 
    </p>
    <div class="footer">
        <br><hr>
        Thank you for staying with us!<br>
        This is a computer-generated invoice.
    </div>
</body>
</html>
