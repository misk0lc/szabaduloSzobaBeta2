<?php

namespace Tests\Feature;

use App\Models\LeaderboardEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaderboardTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_can_fetch_leaderboard(): void
    {
        $user = User::factory()->create(['IsActive' => true]);
        LeaderboardEntry::create([
            'UserID'          => $user->UserID,
            'Score'           => 100,
            'LevelsCompleted' => 3,
            'TimeTotal'       => 600,
            'HintsUsed'       => 2,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->getJson('/api/leaderboard');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => ['UserID', 'Username', 'Score', 'LevelsCompleted', 'TimeTotal', 'HintsUsed'],
                 ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function leaderboard_is_sorted_by_score_descending(): void
    {
        $user1 = User::factory()->create(['IsActive' => true]);
        $user2 = User::factory()->create(['IsActive' => true]);

        LeaderboardEntry::create([
            'UserID' => $user1->UserID, 'Score' => 50,
            'LevelsCompleted' => 1, 'TimeTotal' => 300, 'HintsUsed' => 0,
        ]);
        LeaderboardEntry::create([
            'UserID' => $user2->UserID, 'Score' => 200,
            'LevelsCompleted' => 5, 'TimeTotal' => 900, 'HintsUsed' => 1,
        ]);

        $token = $user1->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->getJson('/api/leaderboard');

        $response->assertStatus(200);
        $data = $response->json();

        $this->assertGreaterThanOrEqual($data[1]['Score'], $data[0]['Score']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function unauthenticated_user_cannot_fetch_leaderboard(): void
    {
        $response = $this->getJson('/api/leaderboard');

        $response->assertStatus(401);
    }
}
