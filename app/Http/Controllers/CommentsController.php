<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Tweet;
use App\Comment;

class CommentsController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    }

    public function store(Request $request)
    {
      $tweet = Tweet::findOrFail($request->tweet_id);
      $comments = $tweet->comments;
      // var_dump($request->all());die;
      Comment::create([
          'text' => request('text'),
          'tweet_id' => request('tweet_id'),
          'user_id' => auth()->id()
      ]);

        return redirect()->route('tweets.show', [
          'comments' => $comments,
          'tweet' => $tweet,
        ]);
    }
}
