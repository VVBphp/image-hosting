<?php

namespace App\Telegram\Commands\Conversation;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;

class CancelCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'cancel';

    /**
     * @var string
     */
    protected $description = 'Прекратить активный диалог';

    /**
     * @var string
     */
    protected $usage = '/cancel';

    /**
     * @var string
     */
    protected $version = '0.3.0';

    /**
     * @var bool
     */
    protected $need_mysql = true;

    /**
     * Main command execution if no DB connection is available
     *
     * @throws TelegramException
     */
    public function executeNoDb(): ServerResponse
    {
        return $this->removeKeyboard('Нечего останавливать.');
    }

    /**
     * Main command execution
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute(): ServerResponse
    {
        $text = 'Нечего останавливать.';

        // Cancel current conversation if any
        $conversation = new Conversation(
            $this->getMessage()->getFrom()->getId(),
            $this->getMessage()->getChat()->getId()
        );

        if ($conversation_command = $conversation->getCommand()) {
            $conversation->cancel();
            $text = 'Диалог "' . $conversation_command . '" остановлен!';
        }

        return $this->removeKeyboard($text);
    }

    /**
     * Remove the keyboard and output a text.
     *
     * @param string $text
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    private function removeKeyboard(string $text): ServerResponse
    {
        return $this->replyToChat($text, [
            'reply_markup' => Keyboard::remove(['selective' => true]),
        ]);
    }
}
