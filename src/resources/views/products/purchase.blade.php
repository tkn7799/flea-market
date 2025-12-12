@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="purchase-container">

    {{-- 左側：商品情報 --}}
    <div class="left-box">

        {{-- 商品画像 --}}
    <div class="product-header">
        <div class="product-image-box">
            @if($product->images->first())
                <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="商品画像">
            @else
                <div class="no-image">商品画像</div>
            @endif
        </div>

        <div class="product-info-text">
                <h2 class="product-name">{{ $product->product_name }}</h2>
                <p class="product-price">¥ {{ number_format($product->price) }}</p>
        </div>
    </div>

        <div class="line"></div>

        {{-- 支払い方法 --}}
        <h3 class="section-title">支払い方法</h3>

        <select id="payment-select" class="payment-select">
            <option value="" disabled selected>選択してください</option>
            <option value="convenience">コンビニ払い</option>
            <option value="card">カード払い</option>
        </select>

        <div class="line"></div>

        <div class="address-section">
        {{-- 配送先 --}}
            <h3 class="section-title">配送先</h3>

            <a class="address-edit" href="{{ route('purchase.address.edit', $product->id) }}">
                変更する
            </a>

            <div class="address-box">
            @if ($address)
                <p>〒 {{ $address->postal_code }}</p>
                <p>{{ $address->address }}</p>
                <p>{{ $address->building }}</p>
            @else
                <p>住所が未登録です</p>
            @endif
            </div>
        </div>

        <div class="line"></div>
    </div>

    {{-- 右側：決済情報 --}}
    <div class="right-box">

        {{-- 支払情報サマリー --}}
        <div class="summary-card">
            <div class="summary-row top-row">
                <span class="label">商品代金</span>
                <span class="value">¥ {{ number_format($product->price) }}</span>
            </div>

            <div class="summary-divider"></div>

            <div class="summary-row bottom-row">
                <span class="label">支払い方法</span>
                <span class="value" id="payment-method-display">未選択</span>
            </div>
        </div>

        {{-- エラー表示 --}}
        @if ($errors->any())
            <div class="error-box">
                @foreach ($errors->all() as $error)
                    <p class="error-text">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- 購入ボタン --}}
        <form action="{{ route('purchase.execute', $product->id) }}" method="POST">
            @csrf

            {{-- JavaScript で値を変更する送信値 --}}
            <input type="hidden" name="payment_method" id="payment-method-hidden" value="">

            <input type="hidden" name="address_id" value="{{ $address->id ?? '' }}">

            <button class="purchase-btn" type="submit">購入する</button>
        </form>
    </div>

</div>

<script>

document.getElementById('payment-select').addEventListener('change', function () {

    const selected = this.value;
    const display = document.getElementById('payment-method-display');
    const hidden = document.getElementById('payment-method-hidden');


    if (selected === "") {
        display.textContent = "未選択";
        hidden.value = "";
        return;
    }
    // 表示テキストを変更
    if (selected === 'convenience') {
        display.textContent = 'コンビニ払い';
    } else if (selected === 'card') {
        display.textContent = 'カード払い';
    }

    // 実際に送信される値を変更
    hidden.value = selected;
});
</script>

@endsection
