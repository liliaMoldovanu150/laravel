@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <form action="{{ route('product.store')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <input
                type="text"
                name="title"
                value="{{ old('title') }}"
                placeholder="{{ __('labels.title') }}"
            >
            @error('title')
            <span class="error">{{ $message }}</span>
            @enderror
            <br><br>
            <textarea
                rows="5"
                cols="20"
                type="text"
                name="description"
                placeholder="{{ __('labels.description') }}"
            >{{ old('description') }}</textarea>
            @error('description')
            <span class="error">{{ $message }}</span>
            @enderror
            <br><br>
            <input
                type="number"
                name="price"
                min="0.00"
                step="0.01"
                value="{{ old('price') }}"
                placeholder="{{ __('labels.price') }}"
            >
            @error('price')
            <span class="error">{{ $message }}</span>
            @enderror
            <br><br>
            <input
                type="file"
                name="image"
            >
            @error('image')
            <span class="error">{{ $message }}</span>
            @enderror
            <br><br>
            <input type="submit" value="{{ __('labels.save') }}">
        </form>
        <a class="go" href="{{ route('product.display') }}">{{ __('labels.products') }}</a>
    </div>
@endsection
