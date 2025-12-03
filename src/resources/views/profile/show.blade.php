@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile_show.css') }}">
@endsection

@section('content')
<div class="mypage">

    {{-- ユーザー基本情報 --}}
    <div class="mypage-header">
        <div class="mypage-header__left">
            <div class="mypage-icon">
                @if($user->profile_image)
                    <img src="{{ asset('storage/' . $user->profile_image) }}" alt="プロフィール画像">
                @else
                    <div class="mypage-icon__placeholder"></div>
                @endif
            </div>
            <div class="mypage-username">{{ $user->user_name }}</div>
        </div>
        <a href="{{ route('profile.edit') }}" class="mypage-edit-btn">プロフィールを編集</a>
    </div>

    {{-- タブメニュー --}}
    <div class="mypage-tabs">
        <a href="/mypage?page=sell" class="mypage-tab {{ $page === 'sell' || $page === null ? 'active' : '' }}">
            出品した商品
        </a>
        <a href="/mypage?page=buy" class="mypage-tab {{ $page === 'buy' ? 'active' : '' }}">
            購入した商品
        </a>
    </div>

    {{-- 商品一覧 --}}
    <div class="mypage-items">

        @php
            if ($page === 'sell' || $page === null) {
                $items = $selling;
            } else {
                $items = $purchased->map(function($purchase){
                    return $purchase->product;
                })->filter();
            }
        @endphp

        @forelse ($items as $item)
        <div class="item-card">
            <a href="/item/{{ $item->id }}">
                <div class="item-card__img">
                    @if($item->images->first())
                        <img src="{{ asset('storage/' . $item->images->first()->image_path) }}">
                    @else
                        <div class="item-card__img--placeholder">商品画像</div>
                    @endif
                </div>
                <div class="item-card__name">{{ $item->product_name }}</div>
            </a>
        </div>
        @empty
            <p class="mypage-empty">商品がありません。</p>
        @endforelse

    </div>

</div>
@endsection
