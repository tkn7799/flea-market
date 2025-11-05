@extends('layouts.app')

@section('title', $product->product_name)

@section('content')
<link rel="stylesheet" href="{{ asset('css/product_detail.css') }}">

<div class="product-detail">

  <div class="product-detail__left">
    @if($product->images->isNotEmpty())
      <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->product_name }}">
    @else
      <img src="{{ asset('images/no_image.png') }}" alt="No image">
    @endif
  </div>

  <div class="product-detail__right">
    <h2 class="product-title">{{ $product->product_name }}</h2>
    <p class="brand-name">{{ $product->brand_name }}</p>
    <p class="price">¥{{ number_format($product->price) }} <span class="tax">（税込）</span></p>

    <div class="product-actions">
      <div class="favorite">
        <span class="icon">★</span>
        <span>3</span>
      </div>
      <div class="comment-count">
        <span class="icon">💬</span>
        <span>{{ $product->comments->count() }}</span>
      </div>
    </div>

    <button class="btn-purchase">購入手続きへ</button>

    <section class="description">
      <h3>商品説明</h3>
      <p>{{ $product->description }}</p>
    </section>

    <section class="info">
      <h3>商品の情報</h3>
      <p><strong>カテゴリー：</strong>
        {{ $product->category->name ?? '未設定' }}
      </p>
      <p><strong>商品の状態：</strong>{{ $product->condition }}</p>
    </section>

    <section class="comments">
      <h3>コメント（{{ $product->comments->count() }}）</h3>
      @foreach($product->comments as $comment)
        <div class="comment">
          <div class="comment-user">
            <div class="user-icon"></div>
            <p class="username">{{ $comment->user->name }}</p>
          </div>
          <p class="comment-body">{{ $comment->comment }}</p>
        </div>
      @endforeach

      <form action="{{ route('comments.store', $product->id) }}" method="POST" class="comment-form">
        @csrf
        <textarea name="comment" placeholder="商品のコメントを入力してください"></textarea>
        <button type="submit" class="btn-comment">コメントを送信する</button>
      </form>
    </section>
  </div>

</div>
@endsection
