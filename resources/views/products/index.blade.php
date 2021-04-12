@extends('layouts.app')

@section('content')

    <div class="content-wrapper">
        @if ($products->count())
            @foreach ($products as $product)
                <div class="product-item">
                    <div class="product-image">
                        <img src="./images/{{ $product->image_url }}" alt="{{ __('labels.product_image') }}">
                    </div>
                    <div class="product-features">
                        <div>{{ $product->title }}</div>
                        <div>{{ $product->description }}</div>
                        <div>{{ number_format($product->price, 2) }}</div>
                    </div>
                    <form action="{{ route('cart.store', $product->id) }}" method="POST">
                        @csrf
                        <input type="submit" value="{{ __('labels.add') }}">
                    </form>
                </div>
                <br>
            @endforeach
        @else
            <p class="message">{{ __('labels.all_added') }}</p>
        @endif
        <a class="go" href="{{ route('cart.show') }}">{{ __('labels.go_to_cart') }}</a>
    </div>

@endsection
