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
                    <a href="{{ route('product.edit', $product->id) }}">{{ __('labels.edit') }}</a>
                    <form action="{{ route('product.destroy', $product->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="submit" value="{{ __('labels.delete') }}">
                    </form>
                </div>
                <br>
            @endforeach
        @endif
        <a href="{{ route('product.create') }}">{{ __('labels.add') }}</a>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <input type="submit" value="{{ __('labels.logout') }}">
        </form>
    </div>
@endsection
