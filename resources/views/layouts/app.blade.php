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
      @if(Auth::check())
      @csrf
        <div class="user_nav grid-6">
          <span>{{ Auth::user()->nickname }}
            <ul class="user__info">
              <li>
                <a href="/users/{{ Auth::id() }}">マイページ</a>
        <form action="/logout" method="POST">
            @csrf
        <a href="javascript:void(0)" onclick="this.parentNode.submit()">ログアウト</a>
        </li>
            </ul>
          </span>
          <a class="post" href="/tweets/create">投稿する</a>
        </div>
        </form>
      @else
        <div class="grid-6">
          <a class="post" href="/login">ログイン</a>
          <a class="post" href="/register">新規登録</a>
        </div>
      @endif
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
