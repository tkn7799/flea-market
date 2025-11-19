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
    <div class="profile-edit__image">
      <div class="profile-edit__circle">
        @if ($user->profile_image)
          <img src="{{ asset('storage/' . $user->profile_image) }}" alt="プロフィール画像">
        @else
          <div class="profile-edit__placeholder"></div>
        @endif
      </div>
      <label class="profile-edit__upload">
        画像を選択する
        <input type="file" name="profile_image" accept="image/*" hidden>
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
@endsection
