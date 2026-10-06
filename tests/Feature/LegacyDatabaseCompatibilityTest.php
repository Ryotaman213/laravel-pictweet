<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\User;
use Tests\TestCase;

class LegacyDatabaseCompatibilityTest extends TestCase
{
    public function test_migrate_keeps_rows_in_an_existing_legacy_schema(): void
    {
        $databaseFile = tempnam(sys_get_temp_dir(), 'pictweet-upgrade-');

        try {
            config([
                'database.default' => 'sqlite',
                'database.connections.sqlite.database' => $databaseFile,
                'database.connections.sqlite.url' => null,
            ]);
            DB::purge('sqlite');

            $this->assertSame(0, Artisan::call('migrate', ['--force' => true]));

            $timestamp = now();
            $userId = DB::table('users')->insertGetId([
                'name' => 'Legacy User',
                'nickname' => 'legacy',
                'email' => 'legacy@example.test',
                'password' => password_hash('legacy-password', PASSWORD_BCRYPT),
                'remember_token' => 'remember-me',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
            $tweetId = DB::table('tweets')->insertGetId([
                'text' => 'Existing post must remain',
                'image' => 'https://images.example.test/legacy.jpg',
                'user_id' => $userId,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
            $commentId = DB::table('comments')->insertGetId([
                'user_id' => $userId,
                'tweet_id' => $tweetId,
                'text' => 'Existing comment must remain',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);

            $columnsBefore = [
                'users' => Schema::getColumnListing('users'),
                'tweets' => Schema::getColumnListing('tweets'),
                'comments' => Schema::getColumnListing('comments'),
                'password_resets' => Schema::getColumnListing('password_resets'),
            ];

            $this->assertSame(0, Artisan::call('migrate', ['--force' => true]));

            $this->assertSame('legacy', DB::table('users')->where('id', $userId)->value('nickname'));
            $this->assertSame('Existing post must remain', DB::table('tweets')->where('id', $tweetId)->value('text'));
            $this->assertSame('Existing comment must remain', DB::table('comments')->where('id', $commentId)->value('text'));
            $this->assertSame($columnsBefore, [
                'users' => Schema::getColumnListing('users'),
                'tweets' => Schema::getColumnListing('tweets'),
                'comments' => Schema::getColumnListing('comments'),
                'password_resets' => Schema::getColumnListing('password_resets'),
            ]);
            $this->assertDatabaseCount('migrations', 6);

            $this->get(route('tweets.index'))
                ->assertOk()
                ->assertSee('Existing post must remain');

            $legacyUser = User::findOrFail($userId);
            $this->post(route('login'), [
                'email' => $legacyUser->email,
                'password' => 'legacy-password',
            ])->assertRedirect(route('tweets.index'));
            $this->assertAuthenticatedAs($legacyUser);
        } finally {
            DB::disconnect('sqlite');
            @unlink($databaseFile);
        }
    }
}
