<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTweetRequest;
use App\Http\Requests\UpdateTweetRequest;
use App\Tweet;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class TweetsController extends Controller
{
    public function index(): View
    {
        $tweets = Tweet::query()
            ->with('user')
            ->orderByDesc('id')
            ->paginate(5);

        return view('tweets.index', compact('tweets'));
    }

    public function create(): View
    {
        return view('tweets.create');
    }

    public function store(StoreTweetRequest $request): View
    {
        $data = $request->validated();

        $request->user()->tweets()->create([
            'text' => $data['text'],
            'image' => $data['image'] ?? '',
        ]);

        return view('tweets.store');
    }

    public function edit(Tweet $tweet): View
    {
        $this->authorize('update', $tweet);

        return view('tweets.edit', compact('tweet'));
    }

    public function update(UpdateTweetRequest $request, Tweet $tweet): View
    {
        $this->authorize('update', $tweet);

        $data = $request->validated();
        $tweet->update([
            'text' => $data['text'],
            'image' => $data['image'] ?? '',
        ]);

        return view('tweets.update');
    }

    public function destroy(Tweet $tweet): View
    {
        $this->authorize('delete', $tweet);
        $tweet->delete();

        return view('tweets.destroy');
    }

    public function show(Tweet $tweet): View
    {
        $tweet->load(['user', 'comments.user']);
        $comments = $tweet->comments;

        return view('tweets.show', compact('tweet', 'comments'));
    }
}
