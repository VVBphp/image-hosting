<?php

namespace App\Telegram\Commands\Conversation;

use App\Helpers\Mime;
use App\Models\BotUser;
use App\Models\Image;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

class GenericmessageCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'genericmessage';

    /**
     * @var string
     */
    protected $description = 'Handle generic message';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * @var bool
     */
    protected $need_mysql = true;

    /**
     * Command execute method if MySQL is required but not available
     *
     * @return ServerResponse
     */
    public function executeNoDb(): ServerResponse
    {
        // Do nothing
        return Request::emptyResponse();
    }

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
        $user = $message->getFrom();
        $user_id = $user->getId();

        // If a conversation is busy, execute the conversation command after handling the message.
        $conversation = new Conversation(
            $message->getFrom()->getId(),
            $message->getChat()->getId()
        );

        // Fetch conversation command if it exists and execute it.
        if ($conversation->exists() && $command = $conversation->getCommand()) {
            return $this->telegram->executeCommand($command);
        }

        $bot = BotUser::find($user_id);
        $user_m = $bot->user;
        $image = new Image();

        $message_type = $message->getType();
        if ($user_m->exists && ($message_type === 'document' || $message_type === 'photo')) {
            $doc = $message->{'get' . ucfirst($message_type)}();
            ($message_type === 'photo') && $doc = end($doc);

            $file_id = $doc->getFileId();
            $file = Request::getFile(['file_id' => $file_id]);

            if ($file->isOk() /*&& Request::downloadFile($file->getResult())*/) {

                $tg_file_path = $file->getResult()->getFilePath();
                $file_path = tempnam(storage_path('images' . DIRECTORY_SEPARATOR . $user_m->id), $user_m->id);
                $client = new Client();

                try {
                    $client->get(
                        'https://api.telegram.org/file/bot' . $this->telegram->getApiKey() . "/{$tg_file_path}",
                        ['sink' => $file_path]
                    );
                    $mime = Mime::Check($file_path);
                    $image->user()->associate($user_m);
                    $image->mime = $mime;

                    $image->removeExif($file_path,
                        storage_path('images' . DIRECTORY_SEPARATOR . $user_m->id) . $image->code,
                        $mime);
                    $image->save();
                    @unlink($file_path);
                    return $this->replyToUser('`' . $image->link . '`', ['parse_mode' => 'markdown']);
                } catch (GuzzleException $e) {
                    return $this->replyToUser('Произошла ошибка, файл не был сохранен');
                }
            }
        }
        return Request::emptyResponse();
    }
}
