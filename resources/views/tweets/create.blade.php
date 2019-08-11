@extends('layouts.app')
@section('content')
<div class="contents row">
    <div class="container">
  <form method="post" action="/tweets">
    @csrf
    <h3>
      投稿する
    </h3>
    <input placeholder="Image Url" type="text" name="image">
    <textarea cols="30" name="text" placeholder="text" rows="10"></textarea>
    <input type="submit" value="SENT">
    </form>
  </div>
</div>
@endsection
