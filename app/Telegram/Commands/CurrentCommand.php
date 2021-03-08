<?php

namespace App\Telegram\Commands;

use App\Telegram\ReplyBuilder;
use Telegram\Bot\Keyboard\Keyboard;

use Telegram\Bot\Laravel\Facades\Telegram;

class CurrentCommand extends BaseCommand
{
    /**
     * @var string Command Name
     */
    protected $name = 'current';

    /**
     * @var array Command Aliases
     */
    protected $aliases = ['current-question', 'c'];

    /**
     * @var string Command Description
     */
    protected $description = 'Текущий вопрос';

    private $alphabet = 'АБВГДЕЖЗИКЛМНОПРСТУФХЦЧШЩЭЮЯ';

    /**
     * {@inheritdoc}
     */
    public function handleTextCommand($contextHandler): ReplyBuilder
    {
        $builder = $contextHandler->makeBuilder();

        $user = $contextHandler
            ->user();

        if ($user->passed) {
            $builder = $builder
                ->text('Вы уже прошли викторину)');
        } else {
            $question = $user
                ->currentQuestion();

            $keyboard = Keyboard::make()
                ->inline();

            foreach ($question->answers->shuffle() as $key => $answer) {
                $keyboard = $keyboard
                    ->row(
                        Keyboard::inlineButton([
                            'text' => mb_substr($this->alphabet, $key, 1) . '. ' . $answer->text,
                            'callback_data' => 'answer ' . $answer->id,
                        ])
                    );
            }

            $builder = $builder
                ->text($question->id . '. ' . $question->text)
                ->markup($keyboard);
        }

        return $builder;
    }
}
