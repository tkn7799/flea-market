@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/product_detail.css') }}">
@endsection

@section('content')
<div class="detail-container">

    {{-- 左側：商品画像 --}}
    <div class="detail-left">
        <div class="detail-image-wrapper">
            @if ($product->images->count())
                <img src="{{ asset('storage/' . $product->images[0]->image_path) }}" class="detail-main-image">
            @else
                <div class="no-image-box">商品画像</div>
            @endif

            @if ($product->status === 'sold')
                <div class="sold-label">Sold</div>
            @endif
        </div>
    </div>

    {{-- 右側：商品情報 --}}
    <div class="detail-right">

        <h2 class="detail-title">{{ $product->product_name }}</h2>
        <p class="detail-brand">{{ $product->brand_name }}</p>

        <p class="detail-price">¥{{ number_format($product->price) }} <span>(税込)</span></p>

    <div class="icon-section">
        {{-- お気に入り --}}
        <div class="icon-group">
            @auth
                <button
                    type="button"
                    class="favorite-btn js-favorite-toggle"
                    data-product-id="{{ $product->id }}"
                >
                    <span class="favorite-heart">
                        {{ $isFavorite ? '❤️' : '🤍' }}
                    </span>
                </button>
            @else
                {{-- 未ログインは login に飛ばすリンク --}}
                <a href="{{ route('login') }}" class="favorite-btn">
                    <span class="favorite-heart">🤍</span>
                </a>
            @endauth

            <span class="icon-count js-favorite-count">
                {{ $product->favorites->count() }}
            </span>
        </div>

        {{-- コメントアイコン --}}
        <div class="icon-group">
            <span class="comment-icon">💬</span>
            <span class="icon-count">{{ $product->comments->count() }}</span>
        </div>
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
                    <div class="comment-user-icon">
                        @if ($comment->user->profile_image)
                            <img src="{{ asset('storage/' . $comment->user->profile_image) }}" alt="icon">
                        @else
                            <div class="comment-user-placeholder"></div>
                        @endif
                    </div>

                    <div class="comment-body-area">
                        <div class="comment-user">{{ $comment->user->user_name }}</div>
                        <div class="comment-body">{{ $comment->comment }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- コメント入力欄（未ログインでも表示） --}}
        <div class="comment-form">
            <h3 class="comment-title">商品へのコメント</h3>

            @auth
                {{-- ログインしている場合：通常の投稿フォーム --}}
                <form action="{{ route('comment.store', $product->id) }}" method="POST">
                    @csrf

                    <textarea
                        name="comment"
                        class="comment-textarea"
                        placeholder="ここにコメントを入力してください"
                    ></textarea>

                    @error('comment')
                        <p class="error-text">{{ $message }}</p>
                    @enderror

                    <button type="submit" class="comment-submit-btn">
                        コメントを送信する
                    </button>
                </form>
            @endauth


            @guest
                {{-- 未ログインの場合 --}}
                <textarea
                    class="comment-textarea"
                    placeholder="コメントを入力するにはログインが必要です"
                    disabled
                ></textarea>

                {{-- ▼ 送信ボタンは押せるが、loginへ飛ばす --}}
                <a href="{{ route('login') }}" class="comment-submit-btn comment-login-btn">
                    コメントを送信する
                </a>
            @endguest
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.querySelectorAll('.js-favorite-toggle').forEach(function (btn) {
        btn.addEventListener('click', async function (e) {
            e.preventDefault();

            const productId = btn.dataset.productId;

            try {
                const response = await fetch(`/favorite/${productId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                });

                // 未ログインだった場合
                if (response.status === 401) {
                    let data = {};
                    try {
                        data = await response.json();
                    } catch (e) {}
                    window.location.href = data.redirect || '{{ route('login') }}';
                    return;
                }

                const data = await response.json();

                const heartEl = btn.querySelector('.favorite-heart');
                const countEl = btn.parentElement.querySelector('.js-favorite-count');

                if (data.status === 'added') {
                    heartEl.textContent = '❤️';
                } else if (data.status === 'removed') {
                    heartEl.textContent = '🤍';
                }

                if (typeof data.count !== 'undefined' && countEl) {
                    countEl.textContent = data.count;
                }

            } catch (error) {
                console.error('favorite error:', error);
            }
        });
    });
});
</script>


@endsection
