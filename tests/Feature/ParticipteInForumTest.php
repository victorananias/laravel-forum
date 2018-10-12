<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Thread;
use App\Reply;

class ParticipteInForumTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unauthenticated_users_may_not_add_replies()
    {
        $this->withExceptionHandling();

        $this->post('/threads/some-channel/1/replies', [])
             ->assertRedirect('/login');
    }

    /** @test */
    public function an_authenticated_user_may_participate_in_forum_threads()
    {
        $user = factory(User::class)->create();

        $this->be($user);

        $thread = factory(Thread::class)->create();

        $reply = factory(Reply::class)->make(['thread_id' => $thread->id]);

        $this->post("{$thread->path()}/replies", $reply->toArray());

        $this->get($thread->path())
             ->assertSee($reply->body);
    }

    /** @test */
    public function a_reply_requires_a_body()
    {
        $this->withExceptionHandling()
             ->actingAs(factory(User::class)->create());

        $thread = factory(Thread::class)->create();

        $reply = factory(Reply::class)->make([
            'body' => null
        ]);

        $this->post("{$thread->path()}/replies", $reply->toArray())
             ->assertSessionHasErrors('body');
    }
}
