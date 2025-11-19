@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="sell-container">

    <h2 class="sell-title">商品の出品</h2>

    {{-- 出品フォーム --}}
    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- 商品画像 --}}
        <div class="form-section">
            <label class="form-label">商品画像</label>
            <div class="image-upload-box">
                <label class="image-select-label">
                    画像を選択する
                    <input type="file" name="images[]" multiple accept="image/*" hidden>
                </label>
            </div>
        </div>

        {{-- 商品の詳細 --}}
        <div class="form-section">
            <h3 class="form-subtitle">商品の詳細</h3>

            {{-- カテゴリー --}}
            <label class="form-label">カテゴリー</label>
            <div class="category-list">
                @foreach($categories as $category)
                <label class="category-tag">
                    <input type="checkbox" name="categories[]" value="{{ $category->id }}">
                    {{ $category->category_name }}
                </label>
                @endforeach
            </div>

            {{-- 商品の状態 --}}
            <label class="form-label">商品の状態</label>
            <select class="form-select" name="condition" required>
                <option value="">選択してください</option>
                <option value="良好">良好</option>
                <option value="目立った傷や汚れなし">目立った傷や汚れなし</option>
                <option value="やや傷や汚れあり">やや傷や汚れあり</option>
                <option value="状態が悪い">状態が悪い</option>
            </select>
        </div>

        {{-- 商品名/ブランド名/説明 --}}
        <div class="form-section">
            <h3 class="form-subtitle">商品名と説明</h3>

            <label class="form-label">商品名</label>
            <input type="text" name="product_name" class="form-input" required>

            <label class="form-label">ブランド名</label>
            <input type="text" name="brand_name" class="form-input">

            <label class="form-label">商品の説明</label>
            <textarea name="description" class="form-textarea" required></textarea>

            <label class="form-label">販売価格</label>
            <input type="number" name="price" class="form-input" placeholder="¥" required>
        </div>

        <div class="form-submit">
            <button type="submit" class="submit-btn">出品する</button>
        </div>

    </form>

</div>
@endsection
