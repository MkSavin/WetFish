<?php

namespace App\Telegram\Commands;

use App\Telegram\ReplyBuilder;

class AboutCommand extends BaseCommand
{
    /**
     * @var string Command Name
     */
    protected $name = 'about';

    /**
     * @var array Command Aliases
     */
    protected $aliases = ['bot', 'a'];

    /**
     * @var string Command Description
     */
    protected $description = 'Что это за бот?';

    /**
     * {@inheritdoc}
     */
    public function handleTextCommand($contextHandler): ReplyBuilder
    {
        return $contextHandler
            ->makeBuilder()
            ->text('Это просто бот)');
    }
}
