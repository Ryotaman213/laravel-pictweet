@extends('layouts.app')
@section('content')
 <div class="contents row">
    @foreach ($tweets as $tweet)
      @include('tweets.partials.card', ['tweet' => $tweet, 'showDetails' => true, 'showActions' => true])
    @endforeach
    {{ $tweets->links() }}
  </div>
@endsection
<style>
ul.pagination {
  display: flex;
  justify-content: center;
}
a.nickname {
  color: white;
}
</style>
