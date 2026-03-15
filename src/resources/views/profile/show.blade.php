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
            <div class="mypage-info-group">
                <div class="mypage-username">{{ $user->user_name }}</div>
                <div class="mypage-stars">
                    @if($averageRating > 0)
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="star {{ $i <= round($averageRating) ? 'star--filled' : 'star--empty' }}">★</span>
                        @endfor
                    @else
                        <span class="no-rating">評価：まだありません</span>
                    @endif
                </div>
            </div>
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
        <a href="/mypage?page=trading" class="mypage-tab {{ $page === 'trading' ? 'active' : '' }}">
            取引中の商品
            @php
                $unreadTradingCount = $purchased->where('status', '!=', 'completed')->filter(function($p) {
                    return $p->messages->where('user_id', '!=', auth()->id())->where('read_at', null)->count() > 0;
                })->count();
            @endphp
            @if($unreadTradingCount > 0)
                <span class="trading-badge">{{ $unreadTradingCount }}</span>
            @endif
        </a>
    </div>

    {{-- 商品一覧 --}}
    <div class="mypage-items">
        @php
            // データの初期化
            $displayItems = collect();

            if ($page === 'sell' || $page === null) {
                $displayItems = $selling ?? collect();
            }
            elseif ($page === 'buy') {
                $displayItems = ($purchased ?? collect())
                    ->where('buyer_id', auth()->id())
                    ->where('status', 'completed');
            }
            elseif ($page === 'trading') {
                $displayItems = ($purchased ?? collect())
                    ->where('status', '!=', 'completed')
                    ->sortByDesc(function($purchase) {
                        $latestMessage = $purchase->messages->sortByDesc('created_at')->first();
                        return $latestMessage ? $latestMessage->created_at : $purchase->created_at;
                    });
            }
        @endphp

        @forelse ($displayItems as $data)
            @php
                $item = ($page === 'sell' || $page === null) ? $data : ($data->product ?? null);
            @endphp
            {{-- itemが存在する場合のみカードを表示 --}}
            @if($item)
                <div class="item-card">
                    @php
                        // リンク先の切り替え
                        $linkUrl = "/item/{$item->id}";
                        $isTrading = false;
                        if ($page === 'trading') {
                            $linkUrl = route('transaction.chat', $data->id);
                            $isTrading = true;
                        } elseif ($page === 'sell' || $page === null) {
                            $activePurchase = $item->purchases()->where('status', '!=', 'completed')->first();
                            if ($activePurchase) {
                                $linkUrl = route('transaction.chat', $activePurchase->id);
                                $isTrading = true;
                            }
                        }
                    @endphp
                    <a href="{{ $linkUrl }}">
                        <div class="item-card__img-container">
                            <div class="item-card__img">
                                @if($item->images && $item->images->first())
                                    <img src="{{ asset('storage/' . $item->images->first()->image_path) }}">
                                @else
                                    <div class="item-card__img--placeholder">商品画像</div>
                                @endif
                            </div>

                            {{-- 取引中タブの時だけ赤いバッジを表示 --}}
                            @if($page === 'trading')
                                @php
                                    $unreadCount = $data->messages
                                    ->where('user_id', '!=', auth()->id())
                                    ->where('read_at', null)
                                    ->count();
                                @endphp
                                @if($unreadCount > 0)
                                    <span class="item-notification-badge">{{ $unreadCount }}</span>
                                @endif
                            @endif
                        </div>
                    </a>
                </div>
            @endif
        @empty
            <p class="mypage-empty">商品がありません。</p>
        @endforelse
    </div>

</div>
@endsection
