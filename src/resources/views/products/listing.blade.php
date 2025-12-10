@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="sell-container">

    <h2 class="sell-title">商品の出品</h2>

    {{-- 出品フォーム --}}
    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf

        {{-- 商品画像 --}}
        <div class="form-section">
            <label class="form-label">商品画像</label>

            <div class="image-upload-box">
                {{-- プレビュー表示 --}}
                <div id="image-preview-area"></div>

                {{-- 画像選択ボタン --}}
                <label class="image-select-label">
                    画像を選択する
                    <input type="file" id="image-input" name="images[]" multiple accept="image/*" hidden>
                </label>
            </div>

            {{-- エラー（images.*） --}}
            @if ($errors->has('images'))
                <p class="error-text">{{ $errors->first('images') }}</p>
            @endif

            @if ($errors->has('images.*'))
                <p class="error-text">{{ $errors->first('images.*') }}</p>
            @endif

        </div>

        {{-- 商品の詳細 --}}
        <div class="form-section">
            <h3 class="form-subtitle">商品の詳細</h3>

            {{-- カテゴリー --}}
            <label class="form-label">カテゴリー</label>
            <div class="category-list">
                @foreach($categories as $category)
                <label class="category-tag {{ in_array($category->id, old('categories', [])) ? 'selected' : '' }}">
                    <input
                        type="checkbox"
                        class="category-checkbox"
                        name="categories[]"
                        value="{{ $category->id }}"
                        {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}
                    >
                    {{ $category->category_name }}
                </label>
                @endforeach
            </div>

            {{-- エラー（categories） --}}
            @error('categories')
                <p class="error-text">{{ $message }}</p>
            @enderror

            {{-- 商品の状態 --}}
            <label class="form-label">商品の状態</label>
            <select class="form-select" name="condition">
                <option value="">選択してください</option>
                <option value="良好" {{ old('condition') === '良好' ? 'selected' : '' }}>良好</option>
                <option value="目立った傷や汚れなし" {{ old('condition') === '目立った傷や汚れなし' ? 'selected' : '' }}>目立った傷や汚れなし</option>
                <option value="やや傷や汚れあり" {{ old('condition') === 'やや傷や汚れあり' ? 'selected' : '' }}>やや傷や汚れあり</option>
                <option value="状態が悪い" {{ old('condition') === '状態が悪い' ? 'selected' : '' }}>状態が悪い</option>
            </select>

            {{-- エラー（condition） --}}
            @error('condition')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        {{-- ● 名前・説明 --}}
        <div class="form-section">
            <h3 class="form-subtitle">商品名と説明</h3>

            {{-- 商品名 --}}
            <label class="form-label">商品名</label>
            <input type="text" name="product_name" class="form-input" value="{{ old('product_name') }}">
            @error('product_name')
                <p class="error-text">{{ $message }}</p>
            @enderror

            {{-- ブランド名 --}}
            <label class="form-label">ブランド名</label>
            <input type="text" name="brand_name" class="form-input" value="{{ old('brand_name') }}">

            {{-- 商品説明 --}}
            <label class="form-label">商品の説明</label>
            <textarea name="description" class="form-textarea">{{ old('description') }}</textarea>
            @error('description')
                <p class="error-text">{{ $message }}</p>
            @enderror

            {{-- 販売価格 --}}
            <label class="form-label">販売価格</label>
            <input type="text" name="price" class="form-input" placeholder="¥" value="{{ old('price') }}">
            @error('price')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        {{-- 送信 --}}
        <div class="form-submit">
            <button type="submit" class="submit-btn">出品する</button>
        </div>

    </form>

</div>

{{-- JS：画像プレビュー・カテゴリ色 --}}
<script>
document.getElementById('image-input').addEventListener('change', function(e) {
    const previewArea = document.getElementById('image-preview-area');
    previewArea.innerHTML = "";

    Array.from(e.target.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = function(event) {
            const img = document.createElement('img');
            img.src = event.target.result;
            img.classList.add('preview-image');
            previewArea.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
});

document.querySelectorAll('.category-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function () {
        if (this.checked) {
            this.parentElement.classList.add('selected');
        } else {
            this.parentElement.classList.remove('selected');
        }
    });
});
</script>
@endsection
