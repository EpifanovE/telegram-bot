<?php

namespace DigitFab\TelegramBot\Classes;

use DigitFab\TelegramBot\Contracts\HttpClientInterface;
use DigitFab\TelegramBot\Contracts\RequestHandlerInterface;
use DigitFab\TelegramBot\Contracts\UpdateInterface;
use DigitFab\TelegramBot\Exceptions\TelegramBotException;

/**
 * Class RequestHandler
 *
 * @method mixed setWebhook($params)
 */
class RequestHandler implements RequestHandlerInterface
{
    private $httpClient;

    private $config;

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
        'getFile',
        'setWebhook',
        'deleteWebhook',
    ];

    private $sendFileMethods = [
        'sendPhoto',
        'sendAudio',
        'sendDocument',
        'sendVideo',
    ];

    private $fileTypes = [
        'photo',
        'audio',
        'document',
        'video',
        'animation',
        'video_note',
        'voice',
        'sticker',
    ];

    public function __construct(HttpClientInterface $httpClient, $config)
    {
        $this->httpClient = $httpClient;
        $this->config     = $config;
    }

    public function __call($methodName, $arguments)
    {
        if ( ! $this->isMethodExists($methodName)) {
            throw new TelegramBotException('Method does not exists in the Request handler.');
        }

        if (in_array($methodName, $this->sendFileMethods)) {
            $fileType = lcfirst(str_replace('send', '', $methodName));

            return $this->sendFile($fileType, $arguments[0]);
        } else {
            $args   = ! empty($arguments) && ! empty($arguments[0]) ? $arguments[0] : [];
            $result = $this->httpClient->post($this->getBaseUrl($methodName), $args);

            return json_decode($result->getBody(), true)['result'];
        }
    }

    public function deleteWebhook() {
        return $this->httpClient->get($this->getBaseUrl('deleteWebhook'));
    }

    public function editMessageMedia($params)
    {
        if (is_file($params['media']['media'])) {
            $fileName = uniqid('file-');

            $sendParams                   = $params;
            $sendParams[$fileName]        = fopen($params['media']['media'], 'r');
            $sendParams['media']['media'] = 'attach://' . $fileName;
            $sendParams['media']          = json_encode($sendParams['media']);

            $this->httpClient->post($this->getBaseUrl('editMessageMedia'), $sendParams);
        }

        if (is_string($params['media']['media']) && filter_var($params['media']['media'], FILTER_VALIDATE_URL)) {
            $this->httpClient->post($this->getBaseUrl('editMessageMedia'), $params);
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

        $this->httpClient->post($this->getSendFileUrl('mediaGroup'), $parameters);
    }

    public function saveFileByFileInfo($fileInfo)
    {
        if (empty($fileInfo['file_path'])) {
            throw new TelegramBotException('file_path is empty');
        }

        $ext = pathinfo($fileInfo['file_path'])['extension'];

        if ( ! is_dir($this->config['uploadsDir'])) {
            $isCreated = mkdir($this->config['uploadsDir'], $this->config['uploadsDirPermissions'] ?? 0775, true);

            if ( ! $isCreated) {
                throw new TelegramBotException('Uploads directory has not been created.');
            }
        }

        $fileName = realpath(rtrim($this->config['uploadsDir'], '/')) . '/' . $fileInfo['file_id'] . '.' . $ext;
        $url      = 'https://api.telegram.org/file/bot' . $this->config['token'] . '/' . ltrim(
                $fileInfo['file_path'],
                '/'
            );

        $this->httpClient->download($url, $fileName);

        return $fileName;
    }

    public function saveFileByUpdate(UpdateInterface $update)
    {
        $fileType = $this->getFileType($update);

        if (empty($fileType)) {
            throw new TelegramBotException('File type field not found in the update object.');
        }

        if ($fileType === 'photo') {
            $photoInfo = $update->getMessage()[$fileType][count($update->getMessage()[$fileType]) - 1];
            $fileId    = $photoInfo['file_id'];
        } else {
            $fileId = $update->getMessage()[$fileType]['file_id'];
        }

        $result = $this->httpClient->post(
            $this->getBaseUrl('getFile'),
            [
                'file_id' => $fileId,
            ]
        );

        $fileInfo = json_decode($result->getBody(), true)['result'];

        return $this->saveFileByFileInfo($fileInfo);
    }

    protected function isMethodExists($methodName)
    {
        return in_array($methodName, $this->availableMethods);
    }

    protected function sendFile($type, $params)
    {
        if (is_file($params[$type])) {
            return $this->sendFileFromDisk($type, $params);
        }

        if (is_string($params[$type]) && filter_var($params[$type], FILTER_VALIDATE_URL)) {
            return $this->httpClient->post($this->getSendFileUrl($type), $params);
        }
    }

    protected function sendFileFromDisk($type, $params)
    {
        $fileContents  = fopen($params[$type], 'r');
        $params[$type] = $fileContents;

        return $this->httpClient->post($this->getSendFileUrl($type), $params);
    }

    protected function getBaseUrl($methodName = '')
    {
        return 'https://api.telegram.org/bot' . $this->config['token'] . '/' . $methodName;
    }

    protected function getSendFileUrl($type)
    {
        return $this->getBaseUrl('send' . ucfirst($type));
    }

    private function getFileType(UpdateInterface $update)
    {
        $intersect = array_values(array_intersect($this->fileTypes, array_keys($update->getMessage())));

        if (count($intersect) > 0) {
            return $intersect[0];
        }

        return null;
    }
}