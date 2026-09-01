<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $purchase->order_id }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 14px;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            background-color: #fff;
        }
        .header-table {
            width: 100%;
            margin-bottom: 40px;
        }
        .header-table td {
            vertical-align: top;
        }
        .title {
            font-size: 28px;
            font-weight: bold;
            color: #4f46e5;
            letter-spacing: 1px;
        }
        .invoice-details {
            text-align: right;
            font-size: 13px;
        }
        .invoice-details strong {
            color: #1e293b;
        }
        .billing-table {
            width: 100%;
            margin-bottom: 40px;
        }
        .billing-table td {
            width: 50%;
            vertical-align: top;
        }
        .billing-title {
            font-size: 12px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .billing-info {
            font-size: 14px;
            color: #1e293b;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        .items-table th {
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            color: #475569;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
            padding: 12px;
            text-align: left;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }
        .items-table .text-right {
            text-align: right;
        }
        .summary-table {
            width: 40%;
            margin-left: auto;
            margin-bottom: 40px;
        }
        .summary-table td {
            padding: 8px 12px;
        }
        .summary-table .label {
            text-align: right;
            color: #64748b;
            font-weight: 500;
        }
        .summary-table .value {
            text-align: right;
            font-weight: bold;
            color: #1e293b;
        }
        .summary-table .grand-total {
            font-size: 18px;
            color: #4f46e5;
            border-top: 2px solid #e2e8f0;
        }
        .footer {
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
            text-align: center;
            color: #94a3b8;
            font-size: 12px;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 11px;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .badge-paid {
            background-color: #dcfce7;
            color: #15803d;
        }
        .badge-pending {
            background-color: #fef9c3;
            color: #a16207;
        }
        .badge-cancelled {
            background-color: #fee2e2;
            color: #b91c1c;
        }
        .badge-failed {
            background-color: #fee2e2;
            color: #b91c1c;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td>
                    <div class="title">GIHQS</div>
                    <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Global Institute for Health & Quality Standards</div>
                </td>
                <td class="invoice-details">
                    <div><strong>Invoice No:</strong> #{{ $purchase->order_id }}</div>
                    <div><strong>Date:</strong> {{ \Carbon\Carbon::parse($purchase->created_at)->format('M d, Y') }}</div>
                    <div style="margin-top: 6px;">
                        <span class="badge badge-{{ strtolower($purchase->payment_status) }}">
                            {{ $purchase->payment_status }}
                        </span>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Billing Info -->
        <table class="billing-table">
            <tr>
                <td>
                    <div class="billing-title">Billed To</div>
                    <div class="billing-info">
                        <strong>{{ $user->full_name ?? ($user->first_name . ' ' . $user->last_name) }}</strong><br>
                        Email: {{ $user->email }}<br>
                        @if($user->phone)
                            Phone: {{ $user->phone }}<br>
                        @endif
                        @if($user->address)
                            Address: {{ $user->address }}, {{ $user->city }} {{ $user->zip }}
                        @endif
                    </div>
                </td>
                <td>
                    <div class="billing-title">Payment Info</div>
                    <div class="billing-info">
                        Method: {{ $purchase->payment_method ?? 'Card' }}<br>
                        Currency: USD ($)<br>
                        Gateway: Stripe
                    </div>
                </td>
            </tr>
        </table>

        <!-- Itemized Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th>Type</th>
                    <th class="text-right">Price</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $item_name }}</strong><br>
                        <span style="font-size: 12px; color: #64748b;">{{ $item_description }}</span>
                    </td>
                    <td>{{ ucfirst($type) }}</td>
                    <td class="text-right">${{ number_format($purchase->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Summary -->
        <table class="summary-table">
            <tr>
                <td class="label">Subtotal:</td>
                <td class="value">${{ number_format($purchase->amount, 2) }}</td>
            </tr>
            <tr>
                <td class="label grand-total">Total Paid:</td>
                <td class="value grand-total">${{ number_format($purchase->amount, 2) }}</td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for your purchase with GIHQS!</p>
            <p style="font-size: 11px;">If you have any questions about this invoice, please contact support.</p>
        </div>
    </div>
</body>
</html>
