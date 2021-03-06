<?php

namespace Tests\Feature;

use Tests\TestCase;

class ReadThreadsTest extends TestCase
{
    //use DatabaseMigrations;

	protected $thread;

	public function setUp()
	{
		parent::setUp();

		$this->thread = create('App\Thread');
	}

    public function test_a_user_can_view_all_threads()
    {
        $response = $this->get('/threads');
        $response->assertSee($this->thread->title);
	}

	public function test_a_user_can_read_a_single_thread()
    {
		$response = $this->get($this->thread->path());
		$response->assertSee($this->thread->title);
	}

	public function test_a_user_can_read_replies_that_are_associated_with_a_thread()
	{
		//een user moet ingelogd zijn
		$this->be(create('App\User'));
		$reply = create('App\Reply', ['thread_id' => $this->thread->id]);

		$response = $this->get($this->thread->path());
		$response->assertSee($reply->body);
	}

	public function test_a_user_can_filter_threads_according_to_a_channel()
    {
        $channel = create('App\Channel');
        $threadInChannel = create('App\Thread', ['channel_id' => $channel->id]);
        $threadNotInChannel = create('App\Thread');

        $this->get('/threads/' . $channel->slug)
            ->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChannel->title);
    }

    public function test_a_user_can_filter_threads_by_any_username()
    {
        $this->signIn(create('App\User', ['name' => 'JohnDoe']));

        $threadByJohn = create('App\Thread', ['user_id' => auth()->id()]);
        $threadNotByJohn = create('App\Thread');

        $this->get('threads?by=JohnDoe')
            ->assertsee($threadByJohn->title)
            ->assertDontSee($threadNotByJohn->title);
    }

    public function test_a_user_can_filter_threads_by_popularity()
    {
        $threadWithTwoReplies = create('App\Thread');
        create('App\Reply', ['thread_id' => $threadWithTwoReplies], 2);

        $threadWithThreeReplies = create('App\Thread');
        create('App\Reply', ['thread_id' => $threadWithThreeReplies], 3);

        $response = $this->getJson('threads?popular=1')->json();

        $this->assertEquals([3, 2, 0] ,array_column($response, 'replies_count'));
    }
}
