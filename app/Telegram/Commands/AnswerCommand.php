<?php

namespace App\Telegram\Commands;

use App\Models\Stat;
use App\Models\User;
use App\Telegram\ReplyBuilder;
use Illuminate\Support\Facades\Log;

class AnswerCommand extends BaseCommand
{
    /**
     * @var string Command Name
     */
    protected $name = 'answer';

    /**
     * @var array Command Aliases
     */
    protected $aliases = ['accept', 'ans'];

    /**
     * @var string Command Description
     */
    protected $description = 'Выбрать ответ';

    /**
     * Processes right answer
     *
     * @param $user
     * @param $answer
     */
    private function processRightAnswer($user, $answer)
    {
        Stat::create([
            'user_id' => $user->id,
            'answer_id' => $answer->id,
            'wrote_text' => '',
        ]);

        // TODO: if checkpoint set progression to all

        $user->setNextQuestion();

        if (empty($user->question->next))
        {
            $user->passed = true;
            $user->save();
        }
    }

    private function findAnswerById($answers, $id)
    {
        return $answers->first(function($answer) use ($id) {
            return $answer->id == $id;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function handleTextCommand($contextHandler): ReplyBuilder
    {
        $builder = $contextHandler->makeBuilder();

        $id = intval($contextHandler->message()->text);
        $user = $contextHandler->user();
        $question = $user->question;

        if ($question->type == 'text') {
            // TODO: text answers
        } else {
            if ($question->type == 'one') {
                $answer = $this->findAnswerById($question->answers, $id);

                $passed = false;
                $answerText = '';

                if (!empty($answer)) {
                    $answerText = $answer->text;

                    if ($answer->right) {
                        $eventType = 'правильный';

                        if (!empty($question->congrat)) {
                            $builder = $builder->text('Поздравление от: *' . $question->congrat->author . '*' . PHP_EOL . $question->congrat->text);

                            if (!empty($question->next)) {
                                $builder = $builder->text('Пойдем дальше?');
                            }
                        } else if (!empty($question->next)) {
                            $builder = $builder->text('Отлично, это правильный ответ!');
                        }

                        if (!empty($question->next)) {
                            $builder = $builder->postCommand('current');
                        } else {
                            $passed = true;
                            $builder = $builder->text('На этом все, спасибо за участие!');
                        }

                        $this->processRightAnswer($user, $answer);
                    } else {
                        $eventType = 'неправильный';

                        $builder = $builder
                            ->text('Что-то не то, не сходится. Похоже, это не правильный ответ.. Попробуй снова!')
                            ->text('Если не выйдет, не забывай, что у тебя есть подсказки: /hint')
                            ->postCommand('current');
                    }
                } else {
                    $eventType = 'Неизвестный';

                    $builder = $builder
                        ->text('Эй, это ответ не на этот вопрос, выбирай правильные! Вот они:')
                        ->postCommand('current');
                }

                User::broadcastToObservers($contextHandler, 'Выбран `' . $eventType . '` ответ.' . PHP_EOL
                    . (!empty($question) ? '*Вопрос*: `' . $question->text . '`' . PHP_EOL : '')
                    . (!empty($answerText) ? '*Ответ*: `' . $answerText . '`' . PHP_EOL : ''));

                if ($passed) {
                    User::broadcastToObservers($contextHandler, 'Викторина пройдена');
                }
            }
            // TODO: multiple answers
        }

        return $builder;
    }
}
