<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel-pictweet</title>
        <link href="{{ asset('css/setting.css') }}" rel="stylesheet">
        <link href="{{ asset('css/style.css') }}" rel="stylesheet">
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    </head>
    <body>
  <header class="header">
    <div class="header__bar row">
      <h1 class="grid-6"><a href="/">Laravel PicTweet</a></h1>
      @auth
        <div class="user_nav grid-6">
          <span>{{ auth()->user()->nickname }}
            <ul class="user__info">
              <li>
                <a href="{{ route('users.show', auth()->id()) }}">マイページ</a>
              </li>
              <li>
                <form action="{{ route('logout') }}" method="POST">
                  @csrf
                  <button type="submit" class="post">ログアウト</button>
                </form>
              </li>
            </ul>
          </span>
          <a class="post" href="{{ route('tweets.create') }}">投稿する</a>
        </div>
      @else
        <div class="grid-6">
          <a class="post" href="{{ route('login') }}">ログイン</a>
          <a class="post" href="{{ route('register') }}">新規登録</a>
        </div>
      @endauth
    </div>
  </header>
        @yield('content')
        <footer>
      <p>
        Copyright Laravel PicTweet 2019.
      </p>
    </footer>
    </body>
</html>
