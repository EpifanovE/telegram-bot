<?php

namespace DigitFab\TelegramBot\Classes;

use DigitFab\TelegramBot\Contracts\HttpClientInterface;
use DigitFab\TelegramBot\Contracts\RequestHandlerInterface;
use DigitFab\TelegramBot\Exceptions\TelegramBotException;

class RequestHandler implements RequestHandlerInterface
{
    private $httpClient;

    private $token;

    private $availableMethods = [
        'sendMessage',
        'sendPhoto',
        'sendAudio',
        'sendDocument',
        'sendVideo',
        'answerCallbackQuery',
        'editMessageText',
        'editMessageCaption',
        'editMessageMedia',
        'editMessageReplyMarkup',
        'stopPoll',
        'deleteMessage',
    ];

    private $sendFileMethods = [
        'sendPhoto',
        'sendAudio',
        'sendDocument',
        'sendVideo',
    ];

    public function __construct(HttpClientInterface $httpClient, $token)
    {
        $this->httpClient = $httpClient;
        $this->token      = $token;
    }

    public function __call($methodName, $arguments)
    {
        if ( ! $this->isMethodExists($methodName)) {
            throw new TelegramBotException('Method does not exists in the Request handler.');
        }

        if (in_array($methodName, $this->sendFileMethods)) {
            $fileType = lcfirst(str_replace('send', '', $methodName));
            $this->sendFile($fileType, $arguments[0]);
        } else {
            $args = ! empty($arguments) && ! empty($arguments[0]) ? $arguments[0] : [];
            $this->httpClient->sendMultipart($this->getBaseUrl($methodName), $args);
        }
    }

    public function editMessageMedia($params)
    {
        if (is_file($params['media']['media'])) {

            $fileName = uniqid('file-');

            $sendParams  = $params;
            $sendParams[$fileName] = fopen($params['media']['media'], 'r');
            $sendParams['media']['media'] = 'attach://' . $fileName;
            $sendParams['media'] = json_encode($sendParams['media']);

            $this->httpClient->sendMultipart($this->getBaseUrl('editMessageMedia'), $sendParams);
        }

        if (is_string($params['media']['media']) && filter_var($params['media']['media'], FILTER_VALIDATE_URL)) {
            $this->httpClient->sendMultipart($this->getBaseUrl('editMessageMedia'), $params);
        }
    }

    public function sendMediaGroup($params)
    {
        $media      = [];
        $parameters = [
            'chat_id' => $params['chat_id'],
        ];

        foreach ($params['media'] as $mediaItem) {
            if (empty($mediaItem['media']) || empty($mediaItem['type'])) {
                continue;
            }

            $tmpMediaItem = $mediaItem;

            $fileName = uniqid('file-');

            if (is_file($mediaItem['media'])) {
                $tmpMediaItem['media'] = 'attach://' . $fileName;
                $parameters[$fileName] = fopen($mediaItem['media'], 'r');
            }

            if (is_string($mediaItem['media']) && filter_var($mediaItem['media'], FILTER_VALIDATE_URL)) {
                $tmpMediaItem['media'] = $mediaItem['media'];
            }

            $media[] = $tmpMediaItem;
        }

        $parameters['media'] = json_encode($media);

        $this->httpClient->sendMultipart($this->getSendFileUrl('mediaGroup'), $parameters);
    }

    protected function isMethodExists($methodName)
    {
        return in_array($methodName, $this->availableMethods);
    }

    protected function sendFile($type, $params)
    {
        if (is_file($params[$type])) {
            $this->sendFileFromDisk($type, $params);
        }

        if (is_string($params[$type]) && filter_var($params[$type], FILTER_VALIDATE_URL)) {
            $this->httpClient->sendMultipart($this->getSendFileUrl($type), $params);
        }
    }

    protected function sendFileFromDisk($type, $params)
    {
        $fileContents  = fopen($params[$type], 'r');
        $params[$type] = $fileContents;

        $this->httpClient->sendMultipart($this->getSendFileUrl($type), $params);
    }

    protected function getBaseUrl($methodName = '')
    {
        return 'https://api.telegram.org/bot' . $this->token . '/' . $methodName;
    }

    protected function getSendFileUrl($type)
    {
        return $this->getBaseUrl('send' . ucfirst($type));
    }
}