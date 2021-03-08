<?php

namespace App\Telegram\Commands;

use App\Telegram\ReplyBuilder;
use Telegram\Bot\Commands\Command;

class HelpCommand extends BaseCommand
{
    /**
     * @var string Command Name
     */
    protected $name = 'help';

    /**
     * @var array Command Aliases
     */
    protected $aliases = ['listcommands', 'h'];

    /**
     * @var string Command Description
     */
    protected $description = 'Помощь и список комманд';

    /**
     * @var array Public commands
     */
    private array $publicCommands = [
        'help', 'about', 'start', 'hint', 'current',
    ];

    /**
     * {@inheritdoc}
     */
    public function handleTextCommand($contextHandler) : ReplyBuilder
    {
        $builder = $contextHandler->makeBuilder();

        $commands = $this->telegram->getCommands();

        $text = '*Доступные команды*:' . PHP_EOL;

        $publicCommands = collect($this->publicCommands);

        foreach ($commands as $name => $handler) {
            if (!$publicCommands->contains($name)) {
                continue;
            }

            if (count($aliases = $handler->getAliases()) > 0) {
                $aliases = ' \[/' . implode(', /', $aliases) . ']';
            } else {
                $aliases = '';
            }

            /* @var Command $handler */
            $text .= sprintf('/%s%s - %s' . PHP_EOL, $name, $aliases, $handler->getDescription());
        }

        $builder->text($text);

        $builder->text('*Легенда*:' . PHP_EOL
            . '`/комманда [/альтернативнаязапись, /к]` - шаблон комманды' . PHP_EOL
            . '`(аргумент)` - обязательный аргумент' . PHP_EOL
            . '`[аргумент]` - необязательный аргумент' . PHP_EOL);

        return $builder;
    }
}
