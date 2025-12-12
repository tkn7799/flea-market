<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Flea Market</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  @yield('css')
</head>

<body>
  @php
    $routeName = Route::currentRouteName();
  @endphp


  <header class="header">
    <div class="header__logo">
          <a href="/"><img src="{{ asset('img/auth-header.png') }}" alt="ロゴ"></a>
    </div>
      @if(!in_array($routeName, ['login', 'register', 'verification.notice']))
          <div class="header__search">
            <form action="{{ route('products.index') }}" method="GET">
              <input
                type="text"
                name="keyword"
                placeholder="なにをお探しですか？"
                value="{{ request('keyword') }}"
                class="search-input"
              >

              {{-- マイリスト中なら維持 --}}
              @if (request('tab') === 'mylist')
                <input type="hidden" name="tab" value="mylist">
              @endif
              <button type="submit" class="search-btn">検索</button>
            </form>
          </div>
          <nav>
          <ul class="header-nav">
            @if(Auth::check())
            <li class="header-nav__item">
              <a class="header-nav__link" href="/mypage">マイページ</a>
            </li>
            <li class="header-nav__item">
                <form class="form" action="/logout" method="post">
                  @csrf
                <button class="header-nav__button">ログアウト</button>
              </form>
            </li>
              @else
            <li class="header-nav__item">
              <a class="header-nav__link" href="/login">ログイン</a>
              <a class="header-nav__link" href="/login">マイページ</a>
            </li>
            @endif
              <a class="header__btn" href="/sell"><li >出品</li></a>
          </ul>
        </nav>
      @endif
  </header>

  <main>
    @yield('content')
  </main>
</body>

</html>
