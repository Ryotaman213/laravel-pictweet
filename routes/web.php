<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\TweetsController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TweetsController::class, 'index'])->name('home');
Route::resource('tweets', TweetsController::class)
    ->only(['create', 'store', 'edit', 'update', 'destroy'])
    ->middleware('auth');
Route::resource('tweets', TweetsController::class)->only(['index', 'show']);

Route::resource('users', UsersController::class)
    ->only(['show'])
    ->middleware('auth');

Route::resource('comments', CommentsController::class)
    ->only(['store'])
    ->middleware('auth');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:login');

    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:30,1');

    Route::get('/password/reset', [ForgotPasswordController::class, 'create'])
        ->name('password.request');
    Route::post('/password/email', [ForgotPasswordController::class, 'store'])
        ->middleware('throttle:password-reset')
        ->name('password.email');
    Route::get('/password/reset/{token}', [ResetPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('/password/reset', [ResetPasswordController::class, 'store'])
        ->middleware('throttle:password-reset')
        ->name('password.update');
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');
