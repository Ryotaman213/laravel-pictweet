@extends('layouts.app')

@section('content')
<div class="contents row">
  <div class="container">
    <h3>{{ __('Reset Password') }}</h3>
    <form method="POST" action="{{ route('password.update') }}">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">

      <label for="email">{{ __('E-Mail Address') }}</label>
      <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
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

      <input type="submit" value="{{ __('Reset Password') }}">
    </form>
  </div>
</div>
@endsection
