<?php

namespace Tests\Unit;

use App\Models\Level;
use App\Models\Question;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LevelModelTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function level_can_be_created_with_fillable_fields(): void
    {
        $level = Level::create([
            'Name'        => 'Teszt Szoba',
            'Description' => 'Egy teszt leírás',
            'Category'    => 'Könnyű',
            'OrderNumber' => 99,
            'IsActive'    => true,
            'BackgroundUrl' => '/rooms/room1/background.png',
        ]);

        $this->assertDatabaseHas('levels', [
            'Name'        => 'Teszt Szoba',
            'OrderNumber' => 99,
        ]);
        $this->assertNotNull($level->LevelID);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function level_is_active_cast_to_boolean(): void
    {
        $level = Level::create([
            'Name'        => 'Bool Teszt',
            'Category'    => 'Könnyű',
            'OrderNumber' => 98,
            'IsActive'    => 1,
        ]);

        $this->assertTrue($level->IsActive);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function level_has_many_questions_relationship(): void
    {
        $level = Level::create([
            'Name'        => 'Kapcsolat Teszt',
            'Category'    => 'Könnyű',
            'OrderNumber' => 97,
            'IsActive'    => true,
        ]);

        Question::create([
            'LevelID'      => $level->LevelID,
            'QuestionText' => 'Mi 2+2?',
            'CorrectAnswer' => '4',
            'RewardDigit'  => 1,
            'MoneyReward'  => 10,
            'PositionX'    => 100,
            'PositionY'    => 200,
        ]);

        $this->assertCount(1, $level->questions);
        $this->assertInstanceOf(Question::class, $level->questions->first());
    }
}
