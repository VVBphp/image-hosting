<?php


namespace App\Telegram\Commands\Conversation;

use App\Models\BotUser;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

class AuthCommand extends \Longman\TelegramBot\Commands\UserCommand
{

    /**
     * @var string
     */
    protected $name = 'auth';

    /**
     * @var string
     */
    protected $description = 'Авторизация в боте';

    /**
     * @var string
     */
    protected $usage = '/auth';

    /**
     * @var string
     */
    protected $version = '0.1.0';

    /**
     * @var bool
     */
    protected $need_mysql = true;

    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * Conversation Object
     *
     * @var Conversation
     */
    protected $conversation;

    /**
     * Main command execution
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute(): ServerResponse
    {
        $message = $this->getMessage();

        $chat = $message->getChat();
        $b_user = $message->getFrom();
        $text = trim($message->getText(true));
        $chat_id = $chat->getId();
        $user_id = $b_user->getId();

        $user = User::whereBotUserId($chat_id)->firstOrNew();

        // Preparing response
        $data = [
            'chat_id' => $chat_id,
            // Remove any keyboard by default
            'reply_markup' => Keyboard::remove(['selective' => true]),
        ];

        $result = Request::emptyResponse();


        if ($chat->isGroupChat() || $chat->isSuperGroup()) {
            // Force reply is applied by default so it can work with privacy on
            $data['reply_markup'] = Keyboard::forceReply(['selective' => true]);
        }

        // Conversation start
        $this->conversation = new Conversation($user_id, $chat_id, $this->getName());

        // Load any existing notes from this conversation
        $notes = &$this->conversation->notes;
        !is_array($notes) && $notes = [];

        // Load the current state of the conversation
        $state = $notes['state'] ?? 0;

        if ($user->exists) {
            Request::sendMessage([
                'chat_id' => $chat_id,
                'text' => 'Этот аккаунт уже привязан к профилю, продолжая вы привяжете аккаунт к другому профилю!' . PHP_EOL .
                    'Если вы не хотите продолжать, нажмите /cancel',
            ]);
        }
        // State machine
        // Every time a step is achieved the state is updated
        switch ($state) {
            case 0:
                if ($text === '') {
                    $notes['state'] = 0;
                    $this->conversation->update();

                    $data['text'] = 'Введите свой логин:';

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['login'] = $text;
                $text = '';

            // No break!
            case 1:
                if ($text === '') {
                    $notes['state'] = 1;
                    $this->conversation->update();

                    $data['text'] = 'Введите свой пароль:';

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['password'] = $text;
                $text = '';

            // No break!
            case 7:
                try {
                    $this->ensureIsNotRateLimited($user_id);

                    $this->conversation->update();
                    if (auth()->once([
                        'login' => $notes['login'],
                        'password' => $notes['password'],
                        'is_active' => 1
                    ])) {
                        if ($old_user = User::whereBotUserId($user_id)->first()) {
                            $old_user->telegram()->disassociate()->save();
                        }
                        $user->telegram()->associate(BotUser::find($user_id))->save();
                        $data['text'] = 'Вы успешно авторизовались.' . PHP_EOL .
                            'Теперь вы можете пользоваться ботом для выгрузки фото. Просто отошлите боту фото и в ответ получите ссылку.';
                        $this->conversation->stop();
                    } else {
                        RateLimiter::hit($this->throttleKey($user_id));
                        $notes['state'] = 0;
                        $notes['login'] = '';
                        $notes['password'] = '';
                        $this->conversation->update();
                        $data['text'] = $user_m->error ?? 'Логин или пароль введены не верно!' . PHP_EOL . 'Попробуйте еще раз. Введите логин:';
                    }

                    $result = Request::sendMessage($data);
                } catch (\Exception $e) {
                    $data['text'] = $e->getMessage();
                    $result = Request::sendMessage($data);
                }

                break;
        }

        return $result;
    }

    /**
     * @throws \Exception
     */
    public function ensureIsNotRateLimited($id)
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($id), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($id));
        throw new \Exception(trans('auth.throttle', [
            'seconds' => $seconds,
            'minutes' => ceil($seconds / 60),
        ]));
    }

    public function throttleKey($id): string
    {
        return Str::transliterate(Str::lower($id));
    }
}
