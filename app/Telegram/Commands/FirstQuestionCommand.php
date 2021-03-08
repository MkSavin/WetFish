<?php

namespace App\Telegram\Commands;

use App\Telegram\ReplyBuilder;

class FirstQuestionCommand extends BaseCommand
{
    /**
     * @var string Command Name
     */
    protected $name = 'first';

    /**
     * @var array Command Aliases
     */
    protected $aliases = ['start-quiz', 'f'];

    /**
     * @var string Command Description
     */
    protected $description = 'Выдать первый вопрос';

    /**
     * {@inheritdoc}
     */
    public function handleTextCommand($contextHandler): ReplyBuilder
    {
        $builder = $contextHandler->makeBuilder();

        if ($contextHandler->user()->quizStarted()) {
            return $builder
                ->text('Викторина уже начата! Продолжай!');
        }

        if ($contextHandler->user()->startQuiz()) {
            return $builder
                ->text('Поехали! 🦼🦼🦼')
                ->postCommand('current');
        }

        return ReplyBuilder::somethingWrong();
    }
}
