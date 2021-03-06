<?php

namespace Tests\Feature;

use App\Models\Topic;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\ActingJWTUser;

class TopicApiTest extends TestCase
{
    use ActingJWTUser;

    protected $user;

    public function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->user = factory(User::class)->create();
    }

    public function testStoreTopic()
    {
        $data = [
            'category_id' => 1,
            'body' => 'test body',
            'title' => 'test title'
        ];

        $response = $this->JWTActingAs($this->user)
            ->json('POST', '/api/topics', $data);

        $assertData = [
            'category_id' => 1,
            'user_id' => $this->user->id,
            'title' => 'test title',
            'body' => clean('test body','user_topic_body')
        ];

        $response->assertStatus(201)
                ->assertJsonFragment($assertData);
    }

    public function testUpdateTopic()
    {
        $topic = $this->makeTopic();

        $data = [
            'category_id' => 2,
            'body' => 'edit body',
            'title' => 'edit title'
        ];

        $response = $this->JWTActingAs($this->user)
            ->json('PATCH','/api/topics/'.$topic->id,$data);

        $assertData = [
            'category_id' => 2,
            'body' => clean('edit body', 'user_topic_body'),
            'title' => 'edit title',
            'user_id' => $this->user->id
        ];

        $response->assertStatus(200)
            ->assertJsonFragment($assertData);
    }

    public function testShowTopic()
    {
        $topic = $this->makeTopic();

        $assertData = [
            'category_id' => $topic->category_id,
            'title' => $topic->title,
            'body' => $topic->body,
            'user_id' => $topic->user_id
        ];

        $response = $this->json('GET', '/api/topics/' . $topic->id);

        $response->assertStatus(200)
            ->assertJsonFragment($assertData);
    }

    public function testIndexTopic()
    {
        $response = $this->json('GET','/api/topics');

        $response->assertStatus(200)
            ->assertJsonStructure(['data','meta']);
    }

    public function testDeleteTopic()
    {
        $topic = $this->makeTopic();


        $this->JWTActingAs($this->user)
            ->json('DELETE','/api/topics/'.$topic->id)
            ->assertStatus(204);

        $this->json('GET', '/api/topics/' . $topic->id)
            ->assertStatus(404);
    }

    protected function makeTopic()
    {
        return factory(Topic::class)->create([
            'user_id' => $this->user->id,
            'category_id' => 1
        ]);
    }
}
