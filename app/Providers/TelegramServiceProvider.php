<?php

namespace App\Providers;

use App\Models\TelegramApi;
use Illuminate\Contracts\Support\DeferrableProvider;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
use PhpTelegramBot\Laravel\Telegram\Commands\CallbackqueryCommand;
use PhpTelegramBot\Laravel\Telegram\Commands\GenericmessageCommand;

class TelegramServiceProvider extends \PhpTelegramBot\Laravel\TelegramServiceProvider implements DeferrableProvider
{

    public function register(): void
    {
        $api = TelegramApi::first();
        if (!$api->exists) return;

        config(['telegram.bot.api_token' => $api->api_token]);
        config(['telegram.bot.username' => $api->username]);
        config(['telegram.bot.api_url' => '']);
        config(['telegram.admins' => '']);

        $this->configureTelegramBot();
    }

    protected function configureTelegramBot()
    {
        $token = config('telegram.bot.api_token');

        if (!$token) {
            return;
        }

        $username = config('telegram.bot.username');

        $apiUrl = config('telegram.bot.api_url', '');
        if (!empty($apiUrl)) {
            Request::setCustomBotApiUri($apiUrl);
        }

        $bot = new Telegram($token, $username);

        // Commands Discovery
        $this->discoverTelegramCommands($bot);
        $bot->addCommandClass(CallbackqueryCommand::class);
        $bot->addCommandClass(GenericmessageCommand::class);

        // Set MySQL Connection
        $connection = app('db')->connection('mysql');
        $bot->enableExternalMySql($connection->getPdo(), 'bot_');

        // Register admins
        $this->registerTelegramAdmins($bot);

        $this->app->instance(Telegram::class, $bot);
    }

    public function provides()
    {
        return [Telegram::class];
    }
}
