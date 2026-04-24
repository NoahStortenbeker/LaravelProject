<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order confirmation</title>
</head>
<body>
    <h1>Thanks for your order</h1>
    <p>Order #{{ $order->id }}</p>
    <p>Status: {{ strtoupper($order->status) }}</p>

    <h2>Shipping</h2>
    <p>{{ $order->shipping_name }}</p>
    <p>{{ $order->shipping_address_line }}</p>
    <p>{{ $order->shipping_zip_code }} {{ $order->shipping_city }}</p>
    <p>{{ $order->shipping_country }}</p>

    <h2>Items</h2>
    <ul>
        @foreach($order->items as $item)
            <li>
                {{ $item->qty }}x {{ $item->name }} — € {{ number_format((float)$item->line_total, 2) }}
            </li>
        @endforeach
    </ul>

    <p><strong>Total:</strong> € {{ number_format((float)$order->total, 2) }}</p>
</body>
</html>
