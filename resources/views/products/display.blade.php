@extends('layouts.app')

@section('content')

    <div class="content-wrapper">
        <h1 class="heading">{{ __('labels.products') }}</h1>
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
                <div class="actions">
                    <a href="{{ route('product.edit', $product->id) }}">{{ __('labels.edit') }}</a>
                    <form action="{{ route('product.destroy', $product->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <input class="link" type="submit" value="{{ __('labels.delete') }}">
                    </form>
                </div>
            </div>
            <br>
        @endforeach
        <div class="actions">
            <a style="display: block" href="{{ route('product.create') }}">{{ __('labels.add') }}</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <input class="link" type="submit" value="{{ __('labels.logout') }}">
            </form>
        </div>
    </div>

@endsection
