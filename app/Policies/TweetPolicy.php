<?php

namespace App\Policies;

use App\Tweet;
use App\User;

class TweetPolicy
{
    public function update(User $user, Tweet $tweet): bool
    {
        return (int) $user->getKey() === (int) $tweet->user_id;
    }

    public function delete(User $user, Tweet $tweet): bool
    {
        return $this->update($user, $tweet);
    }
}
