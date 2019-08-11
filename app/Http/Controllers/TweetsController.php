<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Tweet;

class TweetsController extends Controller
{

  public function __construct()
    {
            $this->middleware('auth')
                 ->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index() {
      $tweets = Tweet::query()->with('user')->orderBy('id', 'DESC')->paginate(5);
      return view('tweets.index', ['tweets' => $tweets]);
    }

    public function create() {
      return view('tweets.create');
    }

    public function store(Request $request) {

       $tweet = new Tweet;
        $tweet->text = $request->text;
        $tweet->image = $request->image;
        $tweet->user_id = Auth::id();
        $tweet->save();

        return view('tweets.store');
    }

    public function destroy($id){
      $tweet = Tweet::find($id);
      if ($tweet->user->id === Auth::id() ) {

        $tweet->delete();

      return view('tweets.destroy');
      }
    }

    public function edit($id) {

      $tweet = Tweet::findOrFail($id);
      return view('tweets.edit', [
        'tweet' => $tweet
      ]);
    }

    public function update(Request $request, $id) {

      $tweet = Tweet::findOrFail($id);
      if ($tweet->user->id === Auth::id() ) {
        $tweet->text = $request->text;
        $tweet->image = $request->image;

        $tweet->save();

        return view('tweets.update');
      }
    }

    public function show($id) {
      $tweet = Tweet::findOrFail($id);
      $comments = $tweet->comments;
      return view('tweets.show', [
        'tweet' => $tweet,
        'comments' => $comments,
      ]);
    }
}
