<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Contracts\View\View;

class UsersController extends Controller
{
    public function show(User $user): View
    {
        $tweets = $user->tweets()
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(5);
        $nickname = $user->nickname;

        return view('users.show', compact('tweets', 'nickname'));
    }
}
