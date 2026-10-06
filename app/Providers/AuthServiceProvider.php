<?php

namespace App\Providers;

use App\Policies\TweetPolicy;
use App\Tweet;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(Tweet::class, TweetPolicy::class);
    }
}
