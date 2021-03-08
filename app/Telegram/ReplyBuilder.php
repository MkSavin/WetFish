<?php

namespace App\Telegram;

use App\Telegram\Commands\BaseCommand;
use Faker\Provider\Base;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;

class ReplyBuilder
{
    public ?ContextHandler $contextHandler;

    public string $parseMode;
    public array $text;
    public string $code = '';
    public Keyboard $markup;
    public array $commands;

    public function __construct(?ContextHandler $contextHandler)
    {
        $this->contextHandler = $contextHandler;
        $this->markdown();
    }

    public function parseMode($parseMode): ReplyBuilder
    {
        $this->parseMode = $parseMode;
        return $this;
    }

    public function html(): ReplyBuilder
    {
        return $this->parseMode('html');
    }

    public function markdown(): ReplyBuilder
    {
        return $this->parseMode('markdown');
    }

    public function text(string $text): ReplyBuilder
    {
        $this->text[] = $text;
        return $this;
    }

    public function code(string $code): ReplyBuilder
    {
        $this->code = $code;
        return $this;
    }

    public function notEnoughArguments(): ReplyBuilder
    {
        return $this->code('notEnoughArguments');
    }

    public function notImplemented(): ReplyBuilder
    {
        return $this->code('notImplemented');
    }

    public function hasExitCode(): bool
    {
        return !empty($this->code);
    }

    public function localizedExitCode(BaseCommand $command)
    {
        $result = '';

        switch ($this->code) {
            case 'notEnoughArguments':
                $result = 'Недостаточно параметров.' . ($command->localizedPattern ? ' Требуемые параметры: `' . $command->localizedPattern . '`.' : '');
                break;
            case 'notImplemented':
                $result = 'Вызван не верный метод';
                break;
        }

        return $result;
    }

    public function markup(Keyboard $markup): ReplyBuilder
    {
        $this->markup = $markup;
        return $this;
    }

    public function replyMessage(BaseCommand $command): array
    {
        if ($this->hasExitCode()) {
            $reply = ['text' => $this->localizedExitCode($command)];

            if (!empty($reply['text'])) {
                $command->replyWithMessage($reply);
            }
        } else {
            $reply = [
                'parse_mode' => $this->parseMode,
            ];

            if (isset($this->markup)) {
                $reply['reply_markup'] = $this->markup->toJson(JSON_PRETTY_PRINT);
            }

            if (empty($this->text)) {
                $this->text = [];
            }

            foreach ($this->text as $text) {
                if (empty($text)) {
                    continue;
                }

                $command->replyWithMessage(array_merge($reply, ['text' => $text]));
            }

            $reply['text'] = $this->text;
        }

        return $reply;
    }

    public function command(string $command, string $group): ReplyBuilder
    {
        $this->commands[$group][] = $command;

        return $this;
    }

    public function preCommand(string $command): ReplyBuilder
    {
        return $this->command($command, 'pre');
    }

    public function postCommand(string $command): ReplyBuilder
    {
        return $this->command($command, 'post');
    }

    public function triggerCommands(BaseCommand $command, string $group = 'post'): array
    {
        $results = [];

        if (isset($this->commands[$group])) {
            foreach ($this->commands[$group] as $postCommand) {
                $results[] = $command->callCommand($postCommand);
            }
        }

        return $results;
    }

    public static function notAuthorized()
    {
        return (new ReplyBuilder(null))
            ->text('Вы не авторизованы в боте, авторизуйтесь коммандой /start');
    }

    public static function somethingWrong()
    {
        return (new ReplyBuilder(null))
            ->text('Что-то пошло не так');
    }

}
