<?php

namespace App\Telegram\Commands;

use \App\Models\User;
use App\Telegram\ContextHandler;
use App\Telegram\ReplyBuilder;
use Illuminate\Support\Facades\Log;
use \Telegram\Bot\Commands\Command;
use \Telegram\Bot\Objects\Update;
use \Telegram\Bot\Objects\Chat;
use function Psy\debug;


/**
 * Base command class
 */
abstract class BaseCommand extends Command
{
    /**
     * @var string Localized command Pattern
     */
    public string $localizedPattern = '';

    /**
     * @var boolean Command needs login flag
     */
    protected bool $needLogin = false;

    private array $nonLoginCommands = [StartCommand::class, ObserveCommand::class, FullObserverCommand::class];

    public function callCommand(string $command)
    {
        $found = false;

        foreach ($this->telegram->getCommands() as $telegramCommand => $handler) {
            if ($telegramCommand == $command) {
                $found = true;
            }
        }

        if ($found) {
            $this->triggerCommand($command);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $contextHandler = new ContextHandler($this);

        $result = ReplyBuilder::notAuthorized();

        if (in_array(get_class($this), $this->nonLoginCommands) || ($contextHandler->user() != null && !empty($contextHandler->user()))) {
            $result = $this->handleTextCommand($contextHandler);
        }

        $result->triggerCommands($this, 'pre');
        $result->replyMessage($this);
        $result->triggerCommands($this, 'post');
    }

    public abstract function handleTextCommand(ContextHandler $contextHandler): ReplyBuilder;

}
