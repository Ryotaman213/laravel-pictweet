@extends('layouts.app')

@section('content')
<div class="contents row">
  <div class="container">
    <h3>新規登録</h3>
    <form method="POST" action="{{ route('register') }}">
      @csrf
      <label for="name">{{ __('Name') }}</label>
      <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
      @error('name')
        <p class="validation-error" role="alert">{{ $message }}</p>
      @enderror

      <label for="nickname">{{ __('NickName') }}</label>
      <input id="nickname" type="text" name="nickname" value="{{ old('nickname') }}" required autocomplete="nickname">
      @error('nickname')
        <p class="validation-error" role="alert">{{ $message }}</p>
      @enderror

      <label for="email">{{ __('E-Mail Address') }}</label>
      <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
      @error('email')
        <p class="validation-error" role="alert">{{ $message }}</p>
      @enderror

      <label for="password">{{ __('Password') }}</label>
      <input id="password" type="password" name="password" required autocomplete="new-password">
      @error('password')
        <p class="validation-error" role="alert">{{ $message }}</p>
      @enderror

      <label for="password-confirm">{{ __('Confirm Password') }}</label>
      <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password">

      <input type="submit" value="{{ __('Register') }}">
    </form>
  </div>
</div>
@endsection
