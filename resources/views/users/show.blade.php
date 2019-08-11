@extends('layouts.app')
@section('content')
  <div class="contents row" >
    <p>{{ $nickname }}さんの投稿一覧</p>
    @foreach($tweets as $tweet)
      <div class="content_post" style="background-image: url( {{ $tweet->image }} );">
        {{ $tweet->text }}
        <span class="name">{{ $tweet->user->nickname }}</span>
      </div>
    @endforeach
    {{ $tweets->links() }}
  </div>
  @endsection
<style>
ul.pagination {
  display: flex;
  justify-content: center;
}
</style>
