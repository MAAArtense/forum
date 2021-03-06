<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ThreadTest extends TestCase
{
	//use DatabaseMigrations;

	protected $threat;

	public function setUp()
	{
		parent::setUp();

		$this->thread = create('App\Thread');
	}

	function test_a_thread_can_make_a_string_path ()
	{
		$this->assertEquals('/threads/' . $this->thread->channel->slug . '/' . $this->thread->id, $this->thread->path());
	}

	function test_a_thread_has_a_creator ()
	{
		$this->assertInstanceOf('App\User', $this->thread->creator);
	}

    function test_a_threat_has_replies()
	{
		$this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->thread->replies);
	}

	function test_a_thread_can_add_a_reply ()
	{
		$this->thread->addReply([
			'body' => 'Foobar',
			'user_id' => 1
		]);

		$this->assertCount(1, $this->thread->replies);
	}

	function test_a_thread_belongs_to_a_channel ()
	{
		$thread = create('App\Thread');

		$this->assertInstanceOf('App\Channel', $thread->channel);

	}
}
