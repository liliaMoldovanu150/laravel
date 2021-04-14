<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('labels.order') }}: {{ $order->id }}</title>
</head>
<body>
<div style="box-sizing: border-box; background-color: #f4f4f4; padding: 10px">
<h2>{{ __('labels.order') }}: {{ $order->id }} {{ __('labels.from') }} {{ $order->created_at->format('d.m.Y') }}</h2>
<div><strong>{{ __('labels.name') }}:</strong> {{ $order->name }}</div>
<p><strong>{{ __('labels.contact_details') }}:</strong> {{ $order->contact_details }}</p>
<p><strong>{{ __('labels.comments') }}:</strong> {{ $order->comments }}</p>
@foreach ($orderProducts as $orderProduct)
    <div style="display: flex;
                justify-content: space-between;
                margin-bottom: 5px;
                width: 700px;
                background-color: #c6c3c3;
                padding: 15px">
        <div style="max-width: 100px; height: 100px; align-self: center">
            <img
                style="width: 100%; height: 100%; object-fit: cover"
                src="{{ url('/') }}/images/{{ $orderProduct->image_url }}"
                alt="{{ __('labels.product_image') }}">
        </div>
        <div style="max-width: 580px; flex-grow: 2">
            <h4>{{ $orderProduct->title }}</h4>
            <p>{{ $orderProduct->description }}</p>
            <div>{{ number_format($orderProduct->price, 2) }}</div>
        </div>
    </div>
@endforeach
<div><strong>{{ __('labels.total_price') }}: {{ number_format($order->total_price, 2) }}</strong></div>
</div>
</body>
</html>
