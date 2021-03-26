@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        @if ($cartProducts->count())
            @foreach ($cartProducts as $cartProduct)
                <div class="product-item">
                    <div class="product-image">
                        <img src="./images/{{ $cartProduct->image_url }}" alt="{{ __('labels.product_image') }}">
                    </div>
                    <div class="product-features">
                        <div>{{ $cartProduct->title }}</div>
                        <div>{{ $cartProduct->description }}</div>
                        <div>{{ number_format($cartProduct->price, 2) }}</div>
                    </div>
                    <form action="{{ route('cart.delete', $cartProduct->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="submit" value="{{ __('labels.remove') }}">
                    </form>
                </div>
                <br>
            @endforeach
            <div class="total-price">{{ __('labels.total_price') }}: {{ number_format($totalPrice, 2) }}</div>
            <form class="cart-form" action="{{ route('order.store') }}" method="POST">
                @csrf
                <input type="hidden" name="totalPrice" value="{{ $totalPrice }}">
                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="{{ __('labels.name') }}"
                >
                @error('name')
                <span class="error">{{ $message }}</span>
                @enderror
                <br><br>
                <input
                    type="text"
                    name="details"
                    value="{{ old('details') }}"
                    placeholder="{{ __('labels.contact_details') }}"
                >
                @error('details')
                <span class="error">{{ $message }}</span>
                @enderror
                <br><br>
                <textarea
                    rows="4"
                    name="comments"
                    placeholder="{{ __('labels.comments') }}">{{ old('comments') }}</textarea>
                <br><br>
                <input type="submit" value="{{ __('labels.checkout') }}">
            </form>
        @else
            <p class="message">{{ __('labels.empty_cart') }}</p>
        @endif
        <a class="go" href="{{ route('product.index') }}">{{ __('labels.go_to_index') }}</a>
    </div>
@endsection
