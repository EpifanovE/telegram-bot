<?php

namespace DigitFab\TelegramBot\Classes;

use DigitFab\TelegramBot\Classes\Update\CallbackQuery;
use DigitFab\TelegramBot\Classes\Update\Message;
use DigitFab\TelegramBot\Classes\Update\UpdateType;
use DigitFab\TelegramBot\Contracts\UpdateInterface;

class Update implements UpdateInterface
{
    const TYPE_CALLBACK_QUERY = 'callback_query';
    const TYPE_TEXT = 'text';

    private $data;

    /**
     * @var UpdateType
     */
    private $type;

    public static function fromServer()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        return new self($data);
    }

    public function __construct($data)
    {
        $this->data = $data;

        if ( ! empty($data['callback_query'])) {
            $this->type = new CallbackQuery(self::TYPE_CALLBACK_QUERY, $data);
        } else {
            $this->type = new Message(self::TYPE_TEXT, $data);
        }
    }

    public function getData()
    {
        return $this->data;
    }

    public function getChatId()
    {
        return $this->type->getChatId();
    }

    public function isType($type): bool
    {
        return $type === $this->type->getName();
    }

    public function getType()
    {
        return $this->type;
    }

    public function getMessage()
    {
        return $this->type->getMessage();
    }

    public function getContent()
    {
        return $this->type->getContent();
    }
}