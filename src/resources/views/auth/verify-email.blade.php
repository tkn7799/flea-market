<h1>メール認証が必要です</h1>
<p>登録したメールアドレスに認証リンクを送信しました。</p>

{{-- 認証メール再送 --}}
<form method="POST" action="{{ route('verification.send') }}">
    @csrf
    <button type="submit">認証メールを再送する</button>
</form>