<?php


namespace App\Telegram\Commands;

use App\Models\User;
use App\Telegram\ReplyBuilder;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class StartCommand extends BaseCommand
{
    /**
     * @var string Command Name
     */
    protected $name = 'start';

    /**
     * @var array Command Aliases
     */
    protected $aliases = ['play', 's'];

    /**
     * @var string Command Description
     */
    protected $description = 'Начать игру 🔥🔥';

    /**
     * {@inheritdoc}
     */
    public function handleTextCommand($contextHandler): ReplyBuilder
    {
        $builder = $contextHandler->makeBuilder();

        $rules = '*Правила*:' . PHP_EOL . 'Бот задает вопрос и выдает несколько вариантов ответа (как правило, их 4) - выбираешь правильный. Все просто. Бот - не монстр, поэтому не сожрет, если ошибешься.' . PHP_EOL;
        $hints = 'Если не получается выбрать правильный ответ - пиши /hint, тогда будет выдана подсказка.' . PHP_EOL
            . 'Подсказок под каждый вопрос несколько, все они классифицируются следующим образом:' . PHP_EOL
            . '*1 подсказка* - общая, к сути вопроса;' . PHP_EOL
            . '*2 подсказка* - наводящая;' . PHP_EOL
            . '*3 подсказка* - ответ "в лоб".';

        $user = $contextHandler->user();

        if (isset($user)) {
            if (!$user->passed) {
                $builder = $builder
                    ->text('Авторизованных не авторизовывают второй раз!')
                    ->text($rules)
                    ->text($hints)
                    ->text('Текущий вопрос:')
                    ->postCommand(!$user->quizStarted() ? 'first' : 'current');
            } else {
                $builder = $builder
                    ->text('Пока что викторину можно пройти только один раз ;)');
            }
        } else {
            $user = User::register($contextHandler->telegramId(), $contextHandler->update);

            User::broadcastToObservers($contextHandler, 'Зарегистрировался новый пользователь' . (!empty($user) ? ': ' . $user->name . ' \[' . $user->id. ']' : ''));

            $builder = $builder
                ->text('[​​​​​​​​​​​](http://dl.mksavin.ru/leo.png) Этот ботяра проверит ваше настроение, и поднимет его с помощью мощных, гидравлических, шуточных, пневмо-вопросов. Ответьте на все вопросы, не расстраивайте Леонида Аркадьевича плиз.')
                ->text($rules)
                ->text($hints)
                ->text('Если не боишься начинать - пиши /first');
        }

        return $builder;
    }
}
