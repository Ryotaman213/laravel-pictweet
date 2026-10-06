@extends('layouts.app')

@section('content')
<div class="contents row">
  <div class="container">
    <h3>ログイン</h3>

    @if (session('status'))
      <p class="form-status" role="status">{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('login') }}">
      @csrf
      <label for="email">{{ __('E-Mail Address') }}</label>
      <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
      @error('email')
        <p class="validation-error" role="alert">{{ $message }}</p>
      @enderror

      <label for="password">{{ __('Password') }}</label>
      <input id="password" type="password" name="password" required autocomplete="current-password">
      @error('password')
        <p class="validation-error" role="alert">{{ $message }}</p>
      @enderror

      <label for="remember">
        <input id="remember" type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
        {{ __('Remember Me') }}
      </label>

      <input type="submit" value="{{ __('Login') }}">
    </form>

    <a href="{{ route('password.request') }}">{{ __('Forgot Your Password?') }}</a>
  </div>
</div>
@endsection
