<?php

namespace Tests\Feature\Post;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;

// use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    // protected User $user;
    // protected User $user2;
    // protected string $authToken;
    // protected $posts;

    // protected function setUp(): void
    // {
    //     parent::setUp();
    //     $this->user = User::factory()->createOne();
    //     $this->authToken = $this->user->createToken('test-token')->plainTextToken;
    //     $this->user2 = User::factory()->createOne();
    //     $this->authToken = $this->user2->createToken('test-token')->plainTextToken;
    //     $this->posts = Post::factory(10)->create(['user_id' => $this->user->id]);

    // }

    public function test_can_list_posts(): void
    {
        $user = User::factory()->createOne();
        $token = $user->createToken('test-token')->plainTextToken;
        $posts = Post::factory(10)->create(['user_id' => $user->id]);

        $response = $this->withToken($token)->getJson('api/posts');

        $response->assertOk();
        $this->assertCount($posts->count(), $response['posts']);
    }


    public function test_can_store_post(): void
    {
        $user = User::factory()->createOne();
        $token = $user->createToken('test-token')->plainTextToken;

        $socialMediaPost = [
            "title" => "Awesome social media post",
            "content" => "Here is the best social media post ever."
        ];

        $response = $this->withToken($token)->postJson('api/posts', $socialMediaPost);
        $response->assertCreated();

        $this->assertEquals($socialMediaPost['title'], $response['post']['title']);
        $this->assertEquals($socialMediaPost['content'], $response['post']['content']);

        $this->assertDatabaseHas('posts', ['id' => 1]);
    }

    public function test_can_show_post(): void
    {
        $user = User::factory()->createOne();
        $token = $user->createToken('test-token')->plainTextToken;
        $post = Post::factory(10)->createOne(['user_id' => $user->id]);

        $response = $this->withToken($token)->getJson('api/posts/'.$post->id);
        $response->assertOk();

        $this->assertEquals($post['title'], $response['post']['title']);
        $this->assertEquals($post['content'], $response['post']['content']);
    }

    public function test_user_cannot_show_another_users_post(): void
    {
        $user = User::factory()->createOne();
        $token = $user->createToken('test-token')->plainTextToken;

        $postOwner = User::factory()->createOne();
        $post = Post::factory(10)->createOne(['user_id' => $postOwner->id]);

        $response = $this->withToken($token)->getJson('api/posts/'.$post->id);

        $response->assertForbidden();
    }

    public function test_can_update_post(): void
    {
        $user = User::factory()->createOne();
        $token = $user->createToken('test-token')->plainTextToken;
        $post = Post::factory(10)->createOne(['user_id' => $user->id]);

        $socialMediaPost = [
            "title" => "Awesome social media post",
            "content" => "Here is the best social media post ever."
        ];

        $response = $this->withToken($token)->putJson('api/posts/'.$post->id, $socialMediaPost);

        $response->assertOk();
        $this->assertEquals($socialMediaPost['title'], $response['post']['title']);
        $this->assertEquals($socialMediaPost['content'], $response['post']['content']);
    }

    public function test_user_cannot_update_another_users_post(): void
    {
        $user = User::factory()->createOne();
        $token = $user->createToken('test-token')->plainTextToken;

        $postOwner = User::factory()->createOne();
        $post = Post::factory(10)->createOne(['user_id' => $postOwner->id]);

        $socialMediaPost = [
            "title" => "Awesome social media post",
            "content" => "Here is the best social media post ever."
        ];

        $response = $this->withToken($token)->putJson('api/posts/'.$post->id, $socialMediaPost);

        $response->assertForbidden();
    }

    public function test_can_delete_post(): void
    {
        $user = User::factory()->createOne();
        $token = $user->createToken('test-token')->plainTextToken;
        $post = Post::factory(10)->createOne(['user_id' => $user->id]);

        $response = $this->withToken($token)->deleteJson('api/posts/'.$post->id);

        $response->assertNoContent();
        $foundPost = Post::find($post->id);
        $this->assertNull($foundPost);
    }

    public function test_user_cannot_delete_another_users_post(): void
    {
        $user = User::factory()->createOne();
        $token = $user->createToken('test-token')->plainTextToken;

        $postOwner = User::factory()->createOne();
        $post = Post::factory(10)->createOne(['user_id' => $postOwner->id]);

        $response = $this->withToken($token)->deleteJson('api/posts/'.$post->id);

        $response->assertForbidden();
    }

    public function test_user_can_generate_post_suggestions_from_topic(): void
    {
        $user = User::factory()->createOne();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)->postJson(
            'api/posts/generate',
            ['topic' => 'Ice cream at the beach']
        );

        $response->assertCreated();
        $this->assertEquals(3, count($response->json('options')));
    }
}
