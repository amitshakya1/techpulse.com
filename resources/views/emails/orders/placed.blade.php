<x-mail::message>
    # Hi {{ $order->user->name }},

    Your order **#{{ $order->id }}** has been placed successfully!

    <x-mail::table>
        | Product | Quantity | Price |
        | ------------- |:--------:| -----:|
        @foreach ($order->items as $item)
            | {{ $item->name }} | {{ $item->quantity }} | ₹{{ $item->price }} |
        @endforeach
    </x-mail::table>

    **Total:** ₹{{ $order->total }}

    <x-mail::button :url="route('orders.show', $order)">
        View Your Order
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
