<?php

namespace App\Http\Controllers;

use App\Models\TelegramApi;
use Exception;
use http\Client;
use Illuminate\Http\Request;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;

class TelegramApisController extends Controller
{
    public function store(Request $request)
    {
        $username = '';
        $request->validate([
            'api_token' => ['nullable', 'string',
                function ($attribute, $value, $fail) use ($username) {
                    try {
                        $username = \Http::get('https://api.telegram.org/bot' . $value . '/getMe')->throw()->json()['result']['username'];
                        $api = new Telegram($value, $username);
                        $api->setWebhook(route('telegram.webhook', $value));
                    } catch (Exception $e) {
                        $fail($e->getMessage());
                    }
                },]
        ]);
        if ($request->filled('api_token')) {
            $telegramApi = TelegramApi::firstOrNew();
            if ($telegramApi->exists) {
                try {
                    $api = new Telegram($telegramApi->api_token);
                    $api->deleteWebhook();
                } catch (TelegramException $e) {
                }
            }
            $telegramApi->api_token = $request->input('api_token');
            $telegramApi->username = $username;
            $telegramApi->save();
        } else {
            $telegramApi = TelegramApi::first();
            if ($telegramApi->exists) {
                try {
                    $api = new Telegram($telegramApi->api_token);
                    $api->deleteWebhook();
                } catch (TelegramException $e) {
                }
                $telegramApi->delete();
            }
        }
        return redirect()->back();
    }
}
