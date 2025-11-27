@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase-address.css') }}">
@endsection

@section('content')
<div class="address-edit-container">

    <h2 class="address-title">住所の変更</h2>

    {{-- エラーメッセージ --}}
    @if ($errors->any())
        <div class="error-box">
            @foreach ($errors->all() as $error)
                <p class="error-text">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('purchase.address.update', $itemId) }}" method="POST">
        @csrf

        @php
            // 入力値優先 → shipping → registered
            $postal = old('postal_code', $shippingAddress->postal_code ?? $registeredAddress->postal_code ?? '');
            $addr   = old('address',     $shippingAddress->address     ?? $registeredAddress->address     ?? '');
            $bldg   = old('building',    $shippingAddress->building    ?? $registeredAddress->building    ?? '');
        @endphp

        {{-- 郵便番号 --}}
        <div class="address-field">
            <label>郵便番号</label>
            <input type="text" name="postal_code" value="{{ $postal }}">
        </div>

        {{-- 住所 --}}
        <div class="address-field">
            <label>住所</label>
            <input type="text" name="address" value="{{ $addr }}">
        </div>

        {{-- 建物名 --}}
        <div class="address-field">
            <label>建物名</label>
            <input type="text" name="building" value="{{ $bldg }}">
        </div>

        {{-- ボタン --}}
        <div class="address-btn-box">
            <button type="submit" class="address-submit-btn">更新する</button>
        </div>

    </form>

</div>
@endsection
