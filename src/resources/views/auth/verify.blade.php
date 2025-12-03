@extends('layouts.app')

@section('content')
<div class="verify-container">
    <h2>メール認証のお願い</h2>

    <p>会員登録ありがとうございます！</p>
    <p>登録メールアドレスに確認メールを送信しています。</p>
    <p>メール内の「認証リンク」をクリックして認証を完了してください。</p>

    @if (session('resent'))
        <div class="alert alert-success">
            認証メールを再送しました。
        </div>
    @endif

    <form method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <button type="submit" class="btn">
            認証メールを再送する
        </button>
    </form>

    <a href="{{ route('login') }}" class="btn">ログイン画面へ戻る</a>
</div>
@endsection
