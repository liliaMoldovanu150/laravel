@extends('layouts.app')

@section('content')


    <div class="orders-wrapper">
        <h1 class="heading">{{ __('labels.orders') }}</h1>
        @foreach ($orders as $order)
            <div class="order">
                <div class="order-details">
                    <div><strong>{{ __('labels.id') }}:</strong> {{ $order->id }}</div>
                    <div><strong>{{ __('labels.date') }}:</strong> {{ $order->created_at->format('d.m.Y') }}</div>
                    <div><strong>{{ __('labels.name') }}:</strong> {{ $order->name }}</div>
                    <div><strong>{{ __('labels.contact_details') }}:</strong> {{ $order->contact_details }}</div>
                    <div><strong>{{ __('labels.comments') }}:</strong> {{ $order->comments }}</div>
                </div>
                <div class="order-products">
                    @foreach ($order->products as $product)
                        <div class="product-item">
                            <div class="product-image order-image">
                                <img src="./images/{{ $product->image_url }}"
                                     alt="{{ __('labels.product_image') }}">
                            </div>
                            <div class="product-features">
                                <div>{{ $product->title }}</div>
                                <div>{{ number_format($product->pivot->product_price, 2) }}</div>
                            </div>
                        </div>
                    @endforeach
                    <div class="total-price"><strong>{{ __('labels.order_total') }}
                            :</strong> {{ number_format($order->total_price, 2) }}</div>
                </div>
            </div>
            <hr>
        @endforeach
    </div>

@endsection
