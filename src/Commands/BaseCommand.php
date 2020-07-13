<?php

namespace DigitFab\TelegramBot\Commands;

use DigitFab\TelegramBot\Contracts\CommandInterface;
use DigitFab\TelegramBot\Contracts\RequestHandlerInterface;
use DigitFab\TelegramBot\Contracts\UpdateInterface;
use DigitFab\TelegramBot\Exceptions\TelegramBotException;

/**
 * Class BaseCommand
 *
 * @method mixed sendMessage($params)
 * @method mixed sendPhoto($params)
 * @method mixed sendAudio($params)
 * @method mixed sendDocument($params)
 * @method mixed sendVideo($params)
 * @method mixed sendMediaGroup($params)
 * @method mixed editMessageText($params)
 * @method mixed editMessageCaption($params)
 * @method mixed editMessageMedia($params)
 * @method mixed editMessageReplyMarkup($params)
 * @method mixed stopPoll($params = [])
 * @method mixed deleteMessage($params = [])
 * @method mixed getFile($params = [])
 *
 * @method mixed saveFileByFileInfo($params = [])
 * @method mixed saveFileByUpdate(UpdateInterface $update)
 *
 * @package DigitFab\Commands
 */
abstract class BaseCommand implements CommandInterface
{
    /**
     * @var UpdateInterface
     */
    protected $update;

    protected $requestHandler;

    protected $attributes = [];

    public function setUpdate(UpdateInterface $update)
    {
        $this->update = $update;
    }

    public function setRequestHandler(RequestHandlerInterface $requestHandler)
    {
        $this->requestHandler = $requestHandler;
    }

    public function setAttributes(array $attributes = []) {
        $this->attributes = $attributes;
    }

    public function __call($methodName, $arguments)
    {
        if (empty($this->update)) {
            throw new TelegramBotException('Update is empty in the Command class');
        }

        if (empty($this->requestHandler)) {
            throw new TelegramBotException('Request handler is empty in the Command class');
        }

        $params = ! empty($arguments) && ! empty($arguments[0]) ? $arguments[0] : [];

        if ($this->isSendAction($methodName)) {
            $chatId = $this->update->getChatId();
            $params = array_merge(
                $params,
                [
                    'chat_id' => $chatId
                ]
            );
        }

        if ($this->isEditAction($methodName)) {
            $chatId = $this->update->getChatId();
            $params = array_merge(
                $params,
                [
                    'chat_id' => $chatId,
                    'message_id' => $this->update->getMessage()['message_id']
                ]
            );
        }

        return call_user_func_array([$this->requestHandler, $methodName], [$params]);
    }

    public function answerCallbackQuery($params = [])
    {
        $callbackQueryId = $this->update->getData()['callback_query']['id'];
        $params          = array_merge(
            [
                'callback_query_id' => $callbackQueryId,
                'text' => '',
            ],
            $params
        );;

        return call_user_func_array([$this->requestHandler, 'answerCallbackQuery'], [$params]);
    }

    protected function isSendAction($methodName)
    {
        $action = substr($methodName, 0, 4);

        return $action === 'send';
    }

    protected function isEditAction($methodName)
    {
        $action = substr($methodName, 0, 4);

        if ($action === 'edit') {
            return true;
        }

        return in_array($methodName, ['stopPoll', 'deleteMessage']);
    }
}