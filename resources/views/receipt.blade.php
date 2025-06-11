<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Receipt {{ $transaction->trx_id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px dashed #ccc;
            padding-bottom: 20px;
        }

        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }

        .shop-info {
            font-size: 14px;
            line-height: 1.4;
            margin-bottom: 15px;
        }

        .transaction-info {
            background-color: #f8f8f8;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            margin-bottom: 5px;
        }

        .info-label {
            font-weight: bold;
            min-width: 120px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 10px;
        }

        .table th {
            background-color: #f2f2f2;
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .tfoot-row {
            font-weight: bold;
            background-color: #f8f8f8;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            border-top: 2px dashed #ccc;
            padding-top: 20px;
        }

        .thank-you {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <!-- Replace with your actual logo path -->
        <img src="{{ public_path('img/logo/Logonameblack.png') }}" alt="Pet Shop Logo" class="logo">
        <div class="title">Pet Shop Receipt</div>
        <div class="shop-info">
            123 Pet Street, Animal City<br>
            Phone: (123) 456-7890 | Website: cindypetshop.my.id<br>
            Email: info@petshopexample.com
        </div>
    </div>

    <div class="transaction-info">
        <div class="info-row">
            <div class="info-label">TRX ID:</div>
            <div>{{ $transaction->trx_id }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Date:</div>
            <div>{{ $transaction->created_at->format('d/m/Y H:i') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Customer:</div>
            <div>{{ $transaction->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Phone:</div>
            <div>{{ $transaction->phone }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Payment Method:</div>
            <div>{{ $transaction->paymentMethod->name }}</div>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Price</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaction->order as $order)
                <tr>
                    <td>
                        @if ($order->product)
                            {{ $order->product->name }}
                        @elseif($order->animal)
                            {{ $order->animal->name }}
                        @elseif($order->grooming)
                            {{ $order->grooming->name }}
                        @elseif($order->hotel)
                            {{ $order->hotel->name }}
                        @elseif($order->breeding)
                            {{ $order->breeding->name }}
                        @else
                            Unknown Item
                        @endif

                        @if ($order->petInformation->isNotEmpty())
                            <div style="font-size: smaller; margin-top: 5px;">
                                <strong>Pet:</strong>
                                @foreach ($order->petInformation as $pet)
                                    {{ $pet->name }} ({{ $pet->age }} years)
                                    @if ($order->hotel)
                                        <br><strong>Stay:</strong>
                                        {{ \Carbon\Carbon::parse($pet->check_in)->format('d/m/Y') }} to
                                        {{ \Carbon\Carbon::parse($pet->check_out)->format('d/m/Y') }}
                                        ({{ $pet->days }} days)
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </td>
                    <td class="text-center">{{ $order->quantity }}</td>
                    <td class="text-right">{{ number_format($order->unit_price, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($order->quantity * $order->unit_price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="tfoot-row">
                <td colspan="3" class="text-right">Total:</td>
                <td class="text-right">{{ number_format($transaction->total_price, 0, ',', '.') }}</td>
            </tr>
            <tr class="tfoot-row">
                <td colspan="3" class="text-right">Paid:</td>
                <td class="text-right">{{ number_format($transaction->paid_amount, 0, ',', '.') }}</td>
            </tr>
            <tr class="tfoot-row">
                <td colspan="3" class="text-right">Change:</td>
                <td class="text-right">{{ number_format($transaction->change_amount, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <div class="thank-you">Thank you for your purchase!</div>
        <div>Please visit us again at cindypetshop.my.id</div>
        {{-- <div style="margin-top: 10px;">
            <strong>Return Policy:</strong> Products can be returned within 7 days with original receipt
        </div> --}}
    </div>
</body>

</html>
