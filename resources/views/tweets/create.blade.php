@extends('layouts.app')
@section('content')
<div class="contents row">
    <div class="container">
  <form method="post" action="/tweets">
    @csrf
    <h3>
      投稿する
    </h3>
    <input placeholder="Image Url" type="url" name="image" value="{{ old('image') }}">
    @error('image')
      <p class="validation-error" role="alert">{{ $message }}</p>
    @enderror
    <textarea cols="30" name="text" placeholder="text" rows="10">{{ old('text') }}</textarea>
    @error('text')
      <p class="validation-error" role="alert">{{ $message }}</p>
    @enderror
    <input type="submit" value="SENT">
    </form>
  </div>
</div>
@endsection
