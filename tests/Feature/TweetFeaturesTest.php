<?php

namespace Tests\Feature;

use App\Comment;
use App\Tweet;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TweetFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_feed_and_tweet_detail_remain_public_and_paginate(): void
    {
        $user = User::factory()->create();
        $tweet = $user->tweets()->create([
            'text' => 'A public post',
            'image' => 'https://images.example.test/cover.jpg',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('A public post')
            ->assertSee('src="https://images.example.test/cover.jpg"', false);

        $this->get(route('tweets.show', $tweet))->assertOk()->assertSee('A public post');

        foreach (range(1, 6) as $index) {
            $user->tweets()->create(['text' => 'Post '.$index, 'image' => '']);
        }

        $this->get(route('tweets.index'))->assertOk()->assertSee('page=2', false);
    }

    public function test_guest_cannot_create_update_delete_comment_or_view_a_private_profile(): void
    {
        $owner = User::factory()->create();
        $tweet = $owner->tweets()->create(['text' => 'Owner post', 'image' => '']);

        $this->get(route('tweets.create'))->assertRedirect(route('login'));
        $this->post(route('tweets.store'), ['text' => 'Guest post'])->assertRedirect(route('login'));
        $this->get(route('tweets.edit', $tweet))->assertRedirect(route('login'));
        $this->patch(route('tweets.update', $tweet), ['text' => 'Guest update'])->assertRedirect(route('login'));
        $this->delete(route('tweets.destroy', $tweet))->assertRedirect(route('login'));
        $this->post(route('comments.store'), ['tweet_id' => $tweet->id, 'text' => 'Guest comment'])
            ->assertRedirect(route('login'));
        $this->get(route('users.show', $owner))->assertRedirect(route('login'));

        $this->assertSame('Owner post', $tweet->fresh()->text);
        $this->assertDatabaseCount('comments', 0);
    }

    public function test_authenticated_user_can_create_a_valid_tweet_and_cannot_choose_its_owner(): void
    {
        $author = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($author)->post(route('tweets.store'), [
            'text' => 'A new post',
            'image' => 'https://images.example.test/a.jpg',
            'user_id' => $otherUser->id,
        ])->assertOk()->assertViewIs('tweets.store');

        $this->assertDatabaseHas('tweets', [
            'text' => 'A new post',
            'image' => 'https://images.example.test/a.jpg',
            'user_id' => $author->id,
        ]);
    }

    public function test_owner_can_patch_a_tweet_without_changing_its_owner(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $tweet = $owner->tweets()->create(['text' => 'Before edit', 'image' => '']);

        $this->actingAs($owner)->patch(route('tweets.update', $tweet), [
            'text' => 'Updated text',
            'image' => 'https://images.example.test/updated.jpg',
            'user_id' => $otherUser->id,
        ])->assertOk()->assertViewIs('tweets.update');

        $this->assertDatabaseHas('tweets', [
            'id' => $tweet->id,
            'text' => 'Updated text',
            'image' => 'https://images.example.test/updated.jpg',
            'user_id' => $owner->id,
        ]);
    }

    public function test_invalid_tweet_text_and_non_http_image_urls_are_rejected(): void
    {
        $author = User::factory()->create();

        $this->actingAs($author)->from(route('tweets.create'))->post(route('tweets.store'), [
            'text' => '',
            'image' => 'https://images.example.test/a.jpg',
        ])->assertRedirect(route('tweets.create'))->assertSessionHasErrors('text');

        $this->from(route('tweets.create'))->post(route('tweets.store'), [
            'text' => 'Unsafe image URL',
            'image' => 'javascript:alert(1)',
        ])->assertRedirect(route('tweets.create'))->assertSessionHasErrors('image');

        $this->assertDatabaseCount('tweets', 0);
    }

    public function test_text_is_limited_to_the_legacy_database_column_byte_capacity(): void
    {
        $author = User::factory()->create();
        $tooManyUtf8Bytes = str_repeat('あ', 21846);

        $this->actingAs($author)->from(route('tweets.create'))->post(route('tweets.store'), [
            'text' => $tooManyUtf8Bytes,
            'image' => '',
        ])->assertRedirect(route('tweets.create'))->assertSessionHasErrors('text');

        $this->assertDatabaseCount('tweets', 0);
    }

    public function test_only_the_owner_can_edit_update_or_delete_a_tweet(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $tweet = $owner->tweets()->create(['text' => 'Original text', 'image' => '']);

        $this->actingAs($owner)->get(route('tweets.edit', $tweet))->assertOk();

        $this->actingAs($other)->get(route('tweets.edit', $tweet))->assertForbidden();
        $this->actingAs($other)->patch(route('tweets.update', $tweet), [
            'text' => 'Changed by another user',
            'image' => '',
        ])->assertForbidden();
        $this->actingAs($other)->delete(route('tweets.destroy', $tweet))->assertForbidden();

        $this->assertSame('Original text', $tweet->fresh()->text);
        $this->assertDatabaseHas('tweets', ['id' => $tweet->id, 'user_id' => $owner->id]);
    }

    public function test_deleting_a_tweet_cascades_to_its_comments(): void
    {
        $owner = User::factory()->create();
        $commenter = User::factory()->create();
        $tweet = $owner->tweets()->create(['text' => 'Delete me', 'image' => '']);
        $comment = new Comment(['text' => 'A comment']);
        $comment->user()->associate($commenter);
        $tweet->comments()->save($comment);

        $this->actingAs($owner)->delete(route('tweets.destroy', $tweet))
            ->assertOk()
            ->assertViewIs('tweets.destroy');

        $this->assertDatabaseMissing('tweets', ['id' => $tweet->id]);
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_missing_tweets_return_not_found_for_read_and_mutation_routes(): void
    {
        $user = User::factory()->create();

        $this->get('/tweets/987654')->assertNotFound();
        $this->actingAs($user)->get('/tweets/987654/edit')->assertNotFound();
        $this->actingAs($user)->patch('/tweets/987654', [
            'text' => 'Missing',
            'image' => '',
        ])->assertNotFound();
        $this->actingAs($user)->delete('/tweets/987654')->assertNotFound();
    }

    public function test_comment_uses_authenticated_user_and_escapes_comment_text(): void
    {
        $owner = User::factory()->create();
        $commenter = User::factory()->create();
        $spoofed = User::factory()->create();
        $tweet = $owner->tweets()->create(['text' => 'Commentable post', 'image' => '']);

        $this->actingAs($commenter)->post(route('comments.store'), [
            'tweet_id' => $tweet->id,
            'text' => '<script>alert(1)</script>',
            'user_id' => $spoofed->id,
        ])->assertRedirect(route('tweets.show', $tweet));

        $this->assertDatabaseHas('comments', [
            'tweet_id' => $tweet->id,
            'user_id' => $commenter->id,
            'text' => '<script>alert(1)</script>',
        ]);

        $this->actingAs($commenter)->get(route('tweets.show', $tweet))
            ->assertOk()
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_invalid_comment_text_or_tweet_id_does_not_create_a_comment(): void
    {
        $user = User::factory()->create();
        $tweet = $user->tweets()->create(['text' => 'Commentable post', 'image' => '']);

        $this->actingAs($user)->from(route('tweets.show', $tweet))->post(route('comments.store'), [
            'tweet_id' => $tweet->id,
            'text' => '',
        ])->assertRedirect(route('tweets.show', $tweet))->assertSessionHasErrors('text');

        $this->from(route('tweets.show', $tweet))->post(route('comments.store'), [
            'tweet_id' => 987654,
            'text' => 'This tweet does not exist',
        ])->assertRedirect(route('tweets.show', $tweet))->assertSessionHasErrors('tweet_id');

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_csrf_middleware_rejects_a_mutation_without_a_token(): void
    {
        $user = User::factory()->create();
        $tweet = $user->tweets()->create(['text' => 'CSRF target', 'image' => '']);
        $environment = $this->app['env'];
        $this->app['env'] = 'production';

        try {
            $this->actingAs($user)->post(route('comments.store'), [
                'tweet_id' => $tweet->id,
                'text' => 'Forged comment',
            ])->assertStatus(419);
        } finally {
            $this->app['env'] = $environment;
        }

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_unsafe_legacy_image_urls_are_not_rendered(): void
    {
        $user = User::factory()->create();
        $tweet = $user->tweets()->create([
            'text' => 'Legacy unsafe image',
            'image' => 'javascript:alert(1)',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Legacy unsafe image')
            ->assertDontSee('javascript:alert(1)', false)
            ->assertDontSee('background-image', false);

        $this->assertNull($tweet->safe_image_url);
    }

    public function test_profile_route_keeps_the_paginated_tweet_list(): void
    {
        $user = User::factory()->create();
        foreach (range(1, 6) as $index) {
            $user->tweets()->create(['text' => 'Profile post '.$index, 'image' => '']);
        }

        $this->actingAs($user)->get(route('users.show', $user))
            ->assertOk()
            ->assertSee($user->nickname)
            ->assertSee('page=2', false);
    }
}
