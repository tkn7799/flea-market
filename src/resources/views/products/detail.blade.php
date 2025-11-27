@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/product_detail.css') }}">
@endsection

@section('content')
<div class="detail-container">

    {{-- 左側：商品画像 --}}
    <div class="detail-left">
        @if ($product->images->count())
            <img src="{{ asset('storage/' . $product->images[0]->image_path) }}" class="detail-main-image">
        @else
            <div class="no-image-box">商品画像</div>
        @endif
    </div>

    {{-- 右側：商品情報 --}}
    <div class="detail-right">

        <h2 class="detail-title">{{ $product->product_name }}</h2>
        <p class="detail-brand">{{ $product->brand_name }}</p>

        <p class="detail-price">¥{{ number_format($product->price) }} <span>(税込)</span></p>

        {{-- お気に入り --}}
        <div class="favorite-section">
            <form action="{{ route('favorite.toggle', $product->id) }}" method="POST">
                @csrf
                <button type="submit" class="favorite-btn">
                    @if($isFavorite)
                        ❤️
                    @else
                        🤍
                    @endif
                </button>
            </form>
            <span>{{ $product->favorites->count() }}</span>
        </div>

        {{-- 購入ボタン --}}
        @if ($product->status === 'selling' && auth()->id() !== $product->user_id)
            <a href="{{ route('purchase.index', $product->id) }}" class="purchase-btn">
                購入手続きへ
            </a>
        @endif

        <hr class="divider">

        {{-- 商品説明 --}}
        <h3 class="section-title">商品説明</h3>
        <p class="detail-description">{{ $product->description }}</p>

        {{-- 商品情報 --}}
        <h3 class="section-title">商品の情報</h3>
        <p>カテゴリー：
            @foreach ($product->categories as $category)
                <span class="category-tag">{{ $category->category_name }}</span>
            @endforeach
        </p>

        <p>商品の状態：{{ $product->condition }}</p>

        <hr class="divider">

        {{-- コメント一覧 --}}
        <div class="comment-list">
            <h3 class="comment-title">コメント ({{ $product->comments->count() }})</h3>

            @foreach ($product->comments as $comment)
                <div class="comment-item">
                    <div class="comment-user">{{ $comment->user->user_name }}</div>
                    <div class="comment-body">{{ $comment->comment }}</div>
                </div>
            @endforeach
        </div>

        {{-- コメント入力欄（未ログインでも表示） --}}
        <div class="comment-form">
            <h3 class="comment-title">商品のコメント</h3>

            <form action="{{ route('comment.store', $product->id) }}" method="POST">
                @csrf

                <textarea
                    name="comment"
                    class="comment-textarea"
                    placeholder="ここにコメントを入力してください"
                    {{ !Auth::check() ? 'disabled' : '' }}
                ></textarea>

                {{-- 未ログインなら注意文言 --}}
                @guest
                    <p class="comment-note">※ コメントするにはログインが必要です</p>
                @endguest

                <button
                    type="submit"
                    class="comment-submit-btn"
                    {{ !Auth::check() ? 'disabled' : '' }}
                >
                    コメントを送信する
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
