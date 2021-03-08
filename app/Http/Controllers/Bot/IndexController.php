<?php

namespace App\Http\Controllers\Bot;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class IndexController extends Controller
{

    protected $commands = ['answer'];

    /**
     * Handle-Контроллер для бота Telegram
     *
     * @return void
     */
    public function handle()
    {
        $update = Telegram::commandsHandler(true);

        if ($update->isType('callback_query')) {
            $query = $update->getCallbackQuery();
            $data = $query->getData();
            $start = strpos($data, ' ');

            $command = ($start !== false) ? substr($data, 0, $start) : substr($data, 1);

            if (in_array($command, $this->commands)) {
                $update->put('message', collect([
                    'text' => substr($data, $start + 1),
                    'from' => $query->getMessage()->getFrom(),
                    'chat' => $query->getMessage()->getChat()
                ]));

                Telegram::triggerCommand($command, $update);
            }
        }
    }

    /**
     * GET-Контроллер для страницы получения данных бота Telegram
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMe()
    {
        $response = Telegram::getMe();

        return response()->json([
            'botId' => $response->getId(),
            'firstName' => $response->getFirstName(),
            'username' => $response->getUsername(),
        ], 200);
    }

}
