@extends('layouts.app')
@section('content')
<div class="contents row">
    @include('tweets.partials.card', ['tweet' => $tweet, 'showDetails' => false, 'showActions' => true])
    <div class="container">
    @auth
      <form method="POST" action="{{ route('comments.store') }}">
        @csrf
          <input name="tweet_id" type="hidden" value="{{ $tweet->id }}">
          <textarea cols="30" name="text" placeholder="コメントする" rows="2">{{ old('text') }}</textarea>
          @error('text')
            <p class="validation-error" role="alert">{{ $message }}</p>
          @enderror
          <input type="submit" value="SENT">
      </form>
    @endauth
      <div class="comments">
      <h4>＜コメント一覧＞</h4>
      @forelse ($comments as $comment)
          <p>
            <strong>
              <a class="post" href="{{ route('users.show', $comment->user) }}">{{ $comment->user->nickname }}：</a>
              </strong>
            {{ $comment->text }}
          </p>
      @empty
        <p>コメントはまだありません。</p>
      @endforelse
    </div>
  </div>
</div>
@endsection
