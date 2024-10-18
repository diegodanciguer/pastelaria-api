<x-mail::message>
# Order Details

Hello, {{ $order->client->name }}!

Your order **#{{ $order->id }}** has been successfully placed on **{{ $order->created_at->format('d/m/Y H:i') }}**.

## Products:

@component('mail::table')
| Product       | Quantity | Unit Price | Total      |
| ------------- |:--------:| ----------:| ----------:|
@foreach($order->products as $product)
| {{ $product->name }} | {{ $product->pivot->quantity }} | $ {{ number_format($product->price, 2, '.', ',') }} | $ {{ number_format($product->price * $product->pivot->quantity, 2, '.', ',') }} |
@endforeach
@endcomponent

**Total Order Value:** $ {{ number_format($order->products->sum(function($product) { return $product->price * $product->pivot->quantity; }), 2, '.', ',') }}

Thank you for choosing our pastry shop!

{{ config('app.name') }}
</x-mail::message>
