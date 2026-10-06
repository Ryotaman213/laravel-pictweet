<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Requests\StoreCommentRequest;
use App\Tweet;
use Illuminate\Http\RedirectResponse;

class CommentsController extends Controller
{
    public function store(StoreCommentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $tweet = Tweet::findOrFail($data['tweet_id']);

        $comment = new Comment(['text' => $data['text']]);
        $comment->user()->associate($request->user());
        $tweet->comments()->save($comment);

        return redirect()->route('tweets.show', ['tweet' => $tweet->getKey()]);
    }
}
