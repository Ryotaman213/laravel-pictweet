@extends('layouts.app')
@section('content')
  <div class="contents row">
    <form action="/tweets/{{$tweet->id}}" method="post">
      @csrf
      @method('PUTCH')
      <h3>
        編集する
      </h3>
      <input placeholder="Image Url" type="text" name="image" value="{{$tweet->image}}" autofocus="true">
      <textarea cols="30" name="text" placeholder="text" rows="10">{{$tweet->text}}</textarea>
      <input type="hidden" name="_method" value="patch">
      <input type="submit" value="SENT">
  </div>
@endsection
