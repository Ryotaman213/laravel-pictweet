<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendPasswordResetLinkRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function create(): View
    {
        return view('auth.passwords.email');
    }

    public function store(SendPasswordResetLinkRequest $request): RedirectResponse
    {
        // Return the same message whether the account exists to avoid email enumeration.
        Password::sendResetLink($request->validated());

        return back()->with('status', 'If an account exists for that email, we have sent a password reset link.');
    }
}
