@extends('layouts.app')

@section('content')

    <div class="orders-wrapper">
        <h1 class="heading">{{ __('labels.orders') }}</h1>
        @foreach ($orders as $order)
            <a href="{{ route('order.show', $order->id) }}" style="color: black; text-decoration: none">
                <div class="order" style="display: flex; justify-content: space-around">
                    <div><strong>{{ __('labels.id') }}:</strong> {{ $order->id }}</div>
                    <div class="total-price"><strong>{{ __('labels.order_total') }}:</strong>
                        {{ number_format($order->total_price, 2) }}
                    </div>
                </div>
            </a>
            <hr>
        @endforeach
    </div>

@endsection
