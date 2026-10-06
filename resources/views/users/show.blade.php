@extends('layouts.app')
@section('content')
  <div class="contents row" >
    <p>{{ $nickname }}さんの投稿一覧</p>
    @foreach ($tweets as $tweet)
      @include('tweets.partials.card', ['tweet' => $tweet, 'showDetails' => false, 'showActions' => false])
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
