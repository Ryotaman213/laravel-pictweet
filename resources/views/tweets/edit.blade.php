@extends('layouts.app')
@section('content')
  <div class="contents row">
    <form action="{{ route('tweets.update', $tweet) }}" method="post">
      @csrf
      @method('PATCH')
      <h3>
        編集する
      </h3>
      <input placeholder="Image Url" type="url" name="image" value="{{ old('image', $tweet->image) }}" autofocus>
      @error('image')
        <p class="validation-error" role="alert">{{ $message }}</p>
      @enderror
      <textarea cols="30" name="text" placeholder="text" rows="10">{{ old('text', $tweet->text) }}</textarea>
      @error('text')
        <p class="validation-error" role="alert">{{ $message }}</p>
      @enderror
      <input type="submit" value="SENT">
    </form>
  </div>
@endsection
