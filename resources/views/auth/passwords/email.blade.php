@extends('layouts.app')

@section('content')
<div class="contents row">
  <div class="container">
    <h3>{{ __('Reset Password') }}</h3>

    @if (session('status'))
      <p class="form-status" role="status">{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
      @csrf
      <label for="email">{{ __('E-Mail Address') }}</label>
      <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
      @error('email')
        <p class="validation-error" role="alert">{{ $message }}</p>
      @enderror
      <input type="submit" value="{{ __('Send Password Reset Link') }}">
    </form>
  </div>
</div>
@endsection
