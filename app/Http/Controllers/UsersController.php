<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Tweet;
use App\User;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show($id) {
      $user = User::find($id);
      $nickname = $user->nickname;
      $tweets = Tweet::query()->with('user')->where('user_id', $user->id )->orderBy("created_at", "DESC")->paginate(5);

      return view('users.show', [
        'tweets' => $tweets,
        'nickname' => $nickname,
      ]);
    }
}
