<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <h1>評価が届きました</h1>
    <p>{{ $purchase->product->product_name }} の取引について、{{ $fromUser->user_name }} さんから評価が届きました。</p>
    
    <p>以下のリンクより、マイページで評価を確認してください。</p>
    
    <a href="{{ route('profile.show') }}">マイページを確認する</a>

    <hr>
    <p>※本メールは自動送信されています。返信はできませんのでご了承ください。</p>
</body>
</html>