<?php

namespace App\Telegram;

use App\Models\User;
use App\Telegram\Commands\BaseCommand;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Objects\Chat;
use Telegram\Bot\Objects\Update;

class ContextHandler
{
    public BaseCommand $command;
    public array $arguments;
    public Update $update;

    private ?Collection $_message = null;
    private ?User $_user = null;
    private int $_telegramId = 0;

    /**
     * ContextHandler constructor.
     *
     * @param BaseCommand $command
     */
    public function __construct(BaseCommand $command)
    {
        $this->command = $command;

        $this->update = $command->getUpdate();
        $this->arguments = $command->getArguments();
    }

    /**
     * Get Telegram message
     *
     * @return mixed
     */
    public function message(): Collection
    {
        if (empty($this->_message)) {
            $this->_message = $this->update->getMessage();
        }

        return $this->_message;
    }

    /**
     * Get current user
     *
     * @return User|null
     */
    public function user(): ?User
    {
        if (empty($this->_user)) {
            $this->_user = User::where('telegram_id', $this->telegramId())->first();
        }

        return $this->_user;
    }

    /**
     * Get telegram ID
     *
     * @return int
     */
    public function telegramId(): int
    {
        if (empty($this->_telegramId)) {
            if ($this->update instanceof Update) {
                $this->_telegramId = $this->message()->getChat()->id;
            } else if ($this->update instanceof Chat) {
                $this->_telegramId = $this->update->id;
            }
        }

        return $this->_telegramId;
    }

    public function makeBuilder(): ReplyBuilder
    {
        return new ReplyBuilder($this);
    }

}
