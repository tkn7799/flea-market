@extends('layouts.app')

@section('title', '商品一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')

<div class="items-page">

    {{-- タブ --}}
    <div class="tabs">
        <a href="{{ url('/') }}" class="tab {{ $tab !== 'mylist' ? 'active' : '' }}">おすすめ</a>
        <a href="{{ url('/?tab=mylist') }}" class="tab {{ $tab === 'mylist' ? 'active' : '' }}">マイリスト</a>
    </div>

    {{-- 商品一覧 --}}
    <div class="item-grid">

        @foreach ($products as $product)
            <a href="{{ route('products.show', $product->id) }}" class="item-card">

                {{-- 商品画像 --}}
                <div class="item-image">
                    @if ($product->images->isNotEmpty())
                        <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->product_name }}">
                    @else
                        <div class="no-image">No Image</div>
                    @endif

                    {{-- 3. Sold 表示（status = sold の場合） --}}
                    @if ($product->status === 'sold')
                        <span class="sold-badge">SOLD</span>
                    @endif
                </div>

                {{-- 商品名 --}}
                <p class="item-name">{{ $product->product_name }}</p>

                {{-- 価格 --}}
                <p class="item-price">¥{{ number_format($product->price) }}</p>

            </a>
        @endforeach

    </div>

</div>

@endsection
