<?php


namespace App\Telegram\Commands;

use App\Models\User;
use App\Telegram\ReplyBuilder;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class HintCommand extends BaseCommand
{
    /**
     * @var string Command Name
     */
    protected $name = 'hint';

    /**
     * @var array Command Aliases
     */
    protected $aliases = ['hints', 'hi'];

    /**
     * @var string Command Description
     */
    protected $description = 'Подсказка';

    /**
     * {@inheritdoc}
     */
    public function handleTextCommand($contextHandler): ReplyBuilder
    {
        $builder = $contextHandler->makeBuilder();

        $user = $contextHandler->user();
        $question = $user->currentQuestion();
        $hints = $question->hints;
        $currentHint = $user->hint;

        $hintsOrdered[0] = $hints->first(function ($hint) {
            return $hint->type == 'general';
        });
        $hintsOrdered[1] = $hints->first(function ($hint) {
            return $hint->type == 'suggestive';
        });
        $hintsOrdered[2] = $hints->first(function ($hint) {
            return $hint->type == 'direct';
        });

        $currentHintLevel = -1;

        if (!empty($currentHint)) {
            for ($i = 0; $i < 3; $i++) {
                if (!empty($hintsOrdered[$i]) && $currentHint->id == $hintsOrdered[$i]->id) {
                    $currentHintLevel = $i;
                }
            }
        }

        if (empty($hintsOrdered[0]) && empty($hintsOrdered[1]) && empty($hintsOrdered[2])) {
            $builder->text('Нее, на этот вопрос у нас подсказок нет');
        } else if ($currentHintLevel < 2) {
            while (empty($hintsOrdered[++$currentHintLevel]) && $currentHintLevel < 3) { }

            if (empty($hintsOrdered[$currentHintLevel])) {
                $builder->text('На этот вопрос больше нет подсказок!');
            } else {
                $localizedLevel = '';

                switch ($currentHintLevel) {
                    case 0:
                        $localizedLevel = 'Общая подсказка';
                        break;
                    case 1:
                        $localizedLevel = 'Наводящая подсказка';
                        break;
                    case 2:
                        $localizedLevel = 'Подсказка "в лоб"';
                        break;
                }

                $user->hint_id = $hintsOrdered[$currentHintLevel]->id;
                $user->save();

                User::broadcastToObservers($contextHandler, 'Получена ' . mb_strtolower($localizedLevel) . '.' . PHP_EOL . 'К *вопросу*: `' . $question->text . '`, ' . PHP_EOL . '*Текст подсказки*: `' . $hintsOrdered[$currentHintLevel]->text . '`');

                $builder->text('*' . $localizedLevel . '*:' . PHP_EOL . $hintsOrdered[$currentHintLevel]->text);
            }
        } else {
            $builder->text('На этот вопрос уже все пасхалки израсходованы!');
        }

        return $builder;
    }
}
