<?php

namespace App\Telegram\Commands;

use App\Models\User;
use App\Telegram\ReplyBuilder;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\Object_;
use Telegram\Bot\Commands\Command;

class ObserveCommand extends BaseCommand
{
    /**
     * @var string Command Name
     */
    protected $name = 'observe';

    /**
     * @var array Command Aliases
     */
    protected $aliases = ['watchdog', 'ob'];

    /**
     * @var string Command Description
     */
    protected $description = 'Просмотр событий';

    /**
     * {@inheritdoc}
     */
    public function handleTextCommand($contextHandler) : ReplyBuilder
    {
        $builder = $contextHandler->makeBuilder();

        $user = $contextHandler->user();

        if (!empty($user) && ($user instanceof User)) {
            if ($user->admin) {
                $user->admin = false;
                $builder->text('Отслеживание отключено');
            } else {
                $user->admin = true;
                $builder->text('Отслеживание включено');
            }
        } else {
            Log::debug('Что-то странное...');
        }

        $user->save();

        User::broadcastToObservers($contextHandler, ($user->admin ? 'Включено' : 'Отключено') . ' отслеживание событий');

        return $builder;
    }
}
