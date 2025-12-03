@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="profile-edit">
  <h2 class="profile-edit__title">プロフィール設定</h2>

  @if (session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- 画像プレビュー --}}
    <div class="profile-edit__image">
      <div class="profile-edit__circle">
        <img 
          id="profile-preview"
          src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : '' }}"
          alt="プロフィール画像"
          style="{{ $user->profile_image ? '' : 'display:none;' }}"
        >
        @unless($user->profile_image)
          <div id="profile-placeholder" class="profile-edit__placeholder"></div>
        @endunless
      </div>

      <label class="profile-edit__upload">
        画像を選択する
        <input id="profile-image-input" type="file" name="profile_image" accept="image/*" hidden>
      </label>

      @error('profile_image')
      <div class="form__error">{{ $message }}</div>
      @enderror
    </div>

    <div class="profile-edit__group">
      <label>ユーザー名</label>
      <input type="text" name="user_name" value="{{ old('user_name', $user->user_name) }}">
      @error('user_name')
      <div class="form__error">{{ $message }}</div>
      @enderror
    </div>

    <div class="profile-edit__group">
      <label>郵便番号</label>
      <input type="text" name="postal_code" value="{{ old('postal_code', $registeredAddress->postal_code ?? '') }}">
      @error('postal_code')
      <div class="form__error">{{ $message }}</div>
      @enderror
    </div>

    <div class="profile-edit__group">
      <label>住所</label>
      <input type="text" name="address" value="{{ old('address', $registeredAddress->address ?? '') }}">
      @error('address')
      <div class="form__error">{{ $message }}</div>
      @enderror
    </div>

    <div class="profile-edit__group">
      <label>建物名</label>
      <input type="text" name="building" value="{{ old('building', $registeredAddress->building ?? '') }}">
      @error('building')
      <div class="form__error">{{ $message }}</div>
      @enderror
    </div>

    <div class="profile-edit__button">
      <button type="submit">更新する</button>
    </div>
  </form>
</div>

{{-- ▼ 画像プレビュー用スクリプト --}}
<script>
document.getElementById('profile-image-input').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (event) {
        const preview = document.getElementById('profile-preview');
        const placeholder = document.getElementById('profile-placeholder');

        preview.src = event.target.result;
        preview.style.display = 'block';

        if (placeholder) placeholder.style.display = 'none';
    };

    reader.readAsDataURL(file);
});
</script>

@endsection
