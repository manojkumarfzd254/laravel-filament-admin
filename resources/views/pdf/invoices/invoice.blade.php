<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; }
        .header, .footer { text-align: center; }
        .footer { position: fixed; bottom: 0; width: 100%; }
        .content { margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Invoice</h1>
        <p>Order ID: #{{ $order->id }}</p>
    </div>

    <div class="content">
        <h3>Customer Details</h3>
        <p><strong>Name:</strong> {{ $order->customer->name }}</p>
        <p><strong>Email:</strong> {{ $order->customer->email }}</p>
        <p><strong>Phone:</strong> {{ $order->customer->phone_number }}</p>

        <h3>Order Products</h3>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->products as $product)
                <tr>
                    <td>{{ $product->product->name }}</td>
                    <td>{{ $product->quantity }}</td>
                    <td>{{ number_format($product->mrp, 2) }}</td>
                    <td>{{ number_format($product->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <h3>Total Amount</h3>
        <p>{{ number_format($order->total_amount, 2) }}</p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} Your Company Name. All Rights Reserved.</p>
    </div>
</body>
</html>
