<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_register_with_valid_data(): void
    {
        $response = $this->postJson('/api/register', [
            'Username' => 'tesztuser',
            'Email'    => 'teszt@example.com',
            'Password' => 'Jelszo123',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'user' => ['UserID', 'Username', 'Email'],
                     'token',
                 ]);

        $this->assertDatabaseHas('users', ['Email' => 'teszt@example.com']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function registration_fails_with_duplicate_email(): void
    {
        User::factory()->create(['Email' => 'dupla@example.com']);

        $response = $this->postJson('/api/register', [
            'Username' => 'masikuser',
            'Email'    => 'dupla@example.com',
            'Password' => 'Jelszo123',
        ]);

        $response->assertStatus(422);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'Email'        => 'belepes@example.com',
            'PasswordHash' => bcrypt('Jelszo123'),
            'IsActive'     => true,
        ]);

        $response = $this->postJson('/api/login', [
            'Email'    => 'belepes@example.com',
            'Password' => 'Jelszo123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['message', 'user', 'token']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function login_fails_with_wrong_password(): void
    {
        User::factory()->create([
            'Email'        => 'rossz@example.com',
            'PasswordHash' => bcrypt('HelytesJelszo1'),
            'IsActive'     => true,
        ]);

        $response = $this->postJson('/api/login', [
            'Email'    => 'rossz@example.com',
            'Password' => 'RosszJelszo9',
        ]);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Hibás email vagy jelszó.']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function inactive_user_cannot_login(): void
    {
        User::factory()->create([
            'Email'        => 'inaktiv@example.com',
            'PasswordHash' => bcrypt('Jelszo123'),
            'IsActive'     => false,
        ]);

        $response = $this->postJson('/api/login', [
            'Email'    => 'inaktiv@example.com',
            'Password' => 'Jelszo123',
        ]);

        $response->assertStatus(403)
                 ->assertJson(['message' => 'A fiók inaktív.']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_can_logout(): void
    {
        $user = User::factory()->create(['IsActive' => true]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->postJson('/api/logout');

        $response->assertStatus(200);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_can_get_own_profile(): void
    {
        $user = User::factory()->create(['IsActive' => true]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->getJson('/api/me');

        $response->assertStatus(200)
                 ->assertJsonFragment(['Email' => $user->Email]);
    }
}
