<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_user_registration_returns_token_and_user(): void
    {
        $registrationData = [
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'brand_name' => 'John\'s Social Media Biz',
            'brand_description' => 'Get ready for a social media revolution because JSM Biz is about to disrupt the social media space with A.I!',
            'website' => 'jsmbiz.example.com',
        ];

        $response = $this->postJson('/api/register', $registrationData);

        $response->assertStatus(201);
        $this->assertNotEmpty($response['token']);
        $response->assertJsonPath('user.name', $registrationData['name']);
        $response->assertJsonPath('user.email', $registrationData['email']);
        $response->assertJsonPath('user.brand_name', $registrationData['brand_name']);
        $response->assertJsonPath('user.brand_description', $registrationData['brand_description']);
        $response->assertJsonPath('user.website', $registrationData['website']);
    }

    public function test_successful_user_login_returns_token_and_user(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        $response = $this->postJson('/api/login', ['email' => $user->email, 'password' => 'password']);

        $response->assertStatus(200);
        $this->assertNotEmpty($response['token']);
    }

    public function test_unsuccessful_user_login_returns_401_status(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        $response = $this->postJson('/api/login', ['email' => $user->email, 'password' => 'wrongpassword']);

        $response->assertStatus(401);
    }
}
