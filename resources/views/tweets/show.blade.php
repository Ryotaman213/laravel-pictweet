@extends('layouts.app')
@section('content')
<div class ="contents row">
      <div class="content_post" style="background-image: url( '{{$tweet->image}}' );">
        @if(Auth::check() && Auth::id() === $tweet->user->id)
        <div class="more">
          <span><img src="{{asset('images/arrow_top.png')}}" alt="Logo"></span>
          <ul class="more_list">
            <li>
              <a class="post" href="/tweets/{{$tweet->id}}/edit">編集</a>
            </li>
            <li>
              <form id="delete" action="{{ route('tweets.destroy',$tweet->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <a class="post" href="javascript:void(0)" onclick="this.parentNode.submit()">削除</a>
              </form>
            </li>
          </ul>
        </div>
        @endif
        <p>{{ $tweet->text }}</p>
        <a class="nickname" href="">
        <span class="name">投稿者{{ $tweet->user->nickname }}</span>
        </a>
      </div>
    <div class="container">
    <!-- ここからフォーム -->
    @if(Auth::check())
      <form  method="POST" action="/comments">
        @csrf
        @method('POST')
          <input name="tweet_id" type="hidden" value="{{ $tweet->id }}">
          <textarea cols="30" name="text" placeholder="コメントする" rows="2"></textarea>
          <input type="submit" value="SENT">
      </form>
    @endif
      <div class="comments">
      <h4>＜コメント一覧＞</h4>
      @if ($comments)
        @foreach ($comments as $comment)
          <p>
            <strong>
              <a class="post" href="/users/{{$comment->user_id}}">{{ $comment->user->nickname}}：</a>
              </strong>
            {{ $comment->text }}
          </p>
        @endforeach
      @endif
    </div>
  </div>
</div>
@endsection
