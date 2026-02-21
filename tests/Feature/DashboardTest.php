<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_dashboard_returns_post_metrics_and_saved_posts(): void
    {
        $user = User::factory()->createOne();
        $token = $user->createToken('test-token')->plainTextToken;

        $posts = Post::factory(10)->create(['user_id' => $user->id]);


        // dd($posts);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->getJson('/api/dashboard');

        $response->assertStatus(200);
        $response->assertJson([
            'total_requests' => 140,
            'total_saved_posts' => $posts->count(),
            'last_generation_time' => $posts->last()->created_at->toJson(),
            // 'posts' => $posts, // no need to assert each post, the above assertions is enough
        ]);
    }
}
