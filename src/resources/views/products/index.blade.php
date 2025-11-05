@extends('layouts.app')

@section('title', '商品一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="items-page">
    {{-- タブ --}}
    <div class="tabs">
        <a href="#" class="tab active">おすすめ</a>
        <a href="#" class="tab">マイリスト</a>
    </div>

    {{-- 商品一覧 --}}
    <div class="item-grid">
        @foreach ($products as $product)
            <div class="item-card">
                <div class="item-image">
                    @if ($product->image_path)
                        <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}">
                    @else
                        <div class="no-image">商品画像</div>
                    @endif
                </div>
                <p class="item-name">{{ $product->name }}</p>
            </div>
        @endforeach
    </div>
</div>
@endsection
