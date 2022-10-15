<?php

namespace  ProgLib\Telegram\Bot\Sdk\Answers;

use BadMethodCallException;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\Update;
use Telegram\Bot\Traits\Telegram;

/**
 * ...
 *
 * @method mixed replyWithPhoto($params) Reply Chat with a Photo. You can use all the sendPhoto() parameters except chat_id.
 * @method mixed replyWithAudio($params) Reply Chat with an Audio message. You can use all the sendAudio() parameters except chat_id.
 * @method mixed replyWithVideo($params) Reply Chat with a Video. You can use all the sendVideo() parameters except chat_id.
 * @method mixed replyWithVoice($params) Reply Chat with a Voice message. You can use all the sendVoice() parameters except chat_id.
 * @method mixed replyWithDocument($params) Reply Chat with a Document. You can use all the sendDocument() parameters except chat_id.
 * @method mixed replyWithSticker($params) Reply Chat with a Sticker. You can use all the sendSticker() parameters except chat_id.
 * @method mixed replyWithLocation($params) Reply Chat with a Location. You can use all the sendLocation() parameters except chat_id.
 * @method mixed replyWithChatAction($params) Reply Chat with a Chat Action. You can use all the sendChatAction() parameters except chat_id.
 *
 * @mixin Api
 */
trait Answerable {

    use Telegram;

    #region Properties

    /**
     * @var Update Входящее обновление.
     */
    protected $update;

    #endregion

    #region Getters

    /**
     * Возвращает обновление.
     *
     * @return Update
     */
    public function getUpdate(): Update {
        return $this->update;
    }

    #endregion

    #region Helpers

    /**
     * Возвращает текст входящего сообщения.
     *
     * @return string
     */
    public function getText() {
        try {
            return ($this->update->has('callback_query'))
                ? $this->update->callbackQuery->data
                : $this->update->getMessage()->text;
        }
        catch (Exception $e) { return ''; }
    }

    #endregion

    /**
     * Magic Method to handle all ReplyWith Methods.
     *
     * @param $method
     * @param $arguments
     *
     * @return mixed|string
     */
    public function __call($method, $arguments) {

        if (!Str::startsWith($method, 'replyWith'))
            throw new BadMethodCallException("Method [$method] does not exist.");

        $reply_name = Str::studly(substr($method, 9));
        $methodName = 'send'.$reply_name;

        if (!method_exists($this->telegram, $methodName))
            throw new BadMethodCallException("Method [$method] does not exist.");

        if (!$this->update->getChat()->has('id'))
            throw new BadMethodCallException("No chat available for reply with [$method].");

        $params = array_merge(['chat_id' => $this->update->getChat()->id], $arguments[0]);

        return call_user_func([$this->telegram, $methodName], $params);
    }

    /**
     * Reply Chat with a message. You can use all the sendMessage() parameters except chat_id.
     *
     * @param  array $params
     * @param  bool  $new
     * @return Message
     * @throws TelegramSDKException
     */
    public function replyWithMessage($params, $new = false) {
        if (!array_key_exists('chat_id', $params)) {
            $chat_id = (!$this->update->getChat()->has('id'))
                ? request()->user()->id
                : $this->update->getChat()->id;
        }
        else
            $chat_id = $params['chat_id'];

        $params = array_merge(
            [ 'chat_id' => $chat_id ],
            config('telegram.message', []),
            $params
        );

        if ($this->update->has('callback_query') & $new !== true) {
            $message = null;
            $message_id = $this->update->getMessage()->messageId;

            // Обязательный ответ на запрос
            try {
                $this->telegram->answerCallbackQuery([
                    'callback_query_id' => $this->update->callbackQuery->id
                ]);
            }
            catch (Exception $e) { }

            // Редактирование текста сообщения
            try {
                $message = $this->telegram->editMessageText(array_merge(Arr::except($params, [ 'reply_markup' ]), [
                    'message_id' => $message_id,
                ]));
            }
            catch (Exception $e) { }

            // Редактирование клавиатуры сообщения
            try {
                if (array_key_exists('reply_markup', $params)) {
                    $message = $this->telegram->editMessageReplyMarkup(array_merge($params, [
                        'message_id' => $message_id
                    ]));
                }
            }
            catch (Exception $e) { }

            return $message;
        }
        else {

            // Отправка статуса набора сообщения
            $this->telegram->sendChatAction([
                'chat_id' => $chat_id,
                'action'  => 'typing'
            ]);

            return $this->telegram->sendMessage($params);
        }
    }
}
