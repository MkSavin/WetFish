<?php

namespace App\Models;

use App\Telegram\ContextHandler;
use App\Traits\Relations\BelongsTo;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class User extends \TCG\Voyager\Models\User
{
    use BelongsTo\Question, BelongsTo\Hint, HasFactory, Notifiable;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'telegram_id',
        'question_id',
        'hint_id',
        'answer_id',
        'text',
        'waits_decision',
        'passed',
        'admin',
        'password',
    ];

    /**
     * Checks is quiz started
     *
     * @return bool
     */
    public function quizStarted()
    {
        return isset($this->question);
    }

    /**
     * Start quiz
     *
     * @return bool
     */
    public function startQuiz()
    {
        $firstQuestion = Question::first();

        $this->question_id = $firstQuestion->id;
        $this->save();

        return $firstQuestion;
    }

    public function setNextQuestion()
    {
        if (!isset($this->question->next)) {
            return false;
        }

        $this->question_id = $this->question->next->id;
        $this->save();

        return true;
    }

    /**
     * Get current question
     *
     * @return mixed
     */
    public function currentQuestion()
    {
        return $this->question;
    }

    public static function broadcastToObservers(ContextHandler $contextHandler, string $description)
    {
        User::where('admin', true)
            ->get()
            ->map(function ($user) use ($contextHandler, $description) {
                try {
                    $contextHandler
                        ->command
                        ->getTelegram()
                        ->sendMessage([
                            'chat_id' => $user->telegram_id,
                            'text' => 'Событие'
                                . (!empty($contextHandler->user())
                                    ? ' от пользователя *' . $contextHandler->user()->name . '*'
                                    : '') . ':' . PHP_EOL . $description,
                            'parse_mode' => 'markdown',
                        ]);
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                }
            });
    }

    /**
     *
     */
    public static function register($telegramId, $update)
    {
        $fromUser = $update->getMessage()->from;

        return User::create([
            'name' => $fromUser->firstName . ' ' . $fromUser->lastName,
            'telegram_id' => $telegramId,
            'text' => '',
            'waits_decision' => false,
            'passed' => false,
            'admin' => false,
            'password' => 'random',
        ]);
    }

}
