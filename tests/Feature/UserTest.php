<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_endpoint_returns_user(): void
    {
        $user = User::factory()->createOne();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->getJson('/api/user');

        $response->assertStatus(200);
        $response->assertJsonPath('user.email', $user->email);
    }

    public function test_user_endpoint_does_not_return_user_when_no_auth_token_is_provided(): void
    {
        // Make sure there is a valid user in the db
        $user = User::factory()->createOne();
        // Not using token but creating it anyway.
        $user->createToken('test-token')->plainTextToken;

        // Purposely exclude the Authorization bearer token
        $response = $this->withHeaders(['Accept' => 'application/json'])->getJson('/api/user');

        $response->assertStatus(401);
    }
}
