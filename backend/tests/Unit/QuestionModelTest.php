<?php

namespace Tests\Unit;

use App\Models\Question;
use App\Models\Hint;
use App\Models\Level;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QuestionModelTest extends TestCase
{
    use RefreshDatabase;

    private Level $level;

    protected function setUp(): void
    {
        parent::setUp();

        $this->level = Level::create([
            'Name'        => 'Kérdés Teszt Szoba',
            'Category'    => 'Könnyű',
            'OrderNumber' => 50,
            'IsActive'    => true,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function question_can_be_created(): void
    {
        $question = Question::create([
            'LevelID'      => $this->level->LevelID,
            'QuestionText' => 'Mi a főváros?',
            'CorrectAnswer' => 'Budapest',
            'RewardDigit'  => 3,
            'MoneyReward'  => 50,
            'PositionX'    => 150,
            'PositionY'    => 250,
        ]);

        $this->assertDatabaseHas('questions', [
            'QuestionText'  => 'Mi a főváros?',
            'CorrectAnswer' => 'Budapest',
        ]);
        $this->assertNotNull($question->QuestionID);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function question_belongs_to_level(): void
    {
        $question = Question::create([
            'LevelID'      => $this->level->LevelID,
            'QuestionText' => 'Melyik szín a piros?',
            'CorrectAnswer' => 'Piros',
            'RewardDigit'  => 1,
            'MoneyReward'  => 10,
            'PositionX'    => 0,
            'PositionY'    => 0,
        ]);

        $this->assertEquals($this->level->LevelID, $question->level->LevelID);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function question_has_many_hints(): void
    {
        $question = Question::create([
            'LevelID'      => $this->level->LevelID,
            'QuestionText' => 'Tipp teszt kérdés',
            'CorrectAnswer' => 'Válasz',
            'RewardDigit'  => 2,
            'MoneyReward'  => 20,
            'PositionX'    => 0,
            'PositionY'    => 0,
        ]);

        Hint::create([
            'QuestionID' => $question->QuestionID,
            'HintText'   => 'Első tipp',
            'Cost'       => 5,
            'HintOrder'  => 1,
        ]);

        Hint::create([
            'QuestionID' => $question->QuestionID,
            'HintText'   => 'Második tipp',
            'Cost'       => 10,
            'HintOrder'  => 2,
        ]);

        $this->assertCount(2, $question->hints);
    }
}
