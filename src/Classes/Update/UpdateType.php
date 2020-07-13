<?php

namespace DigitFab\TelegramBot\Classes\Update;

use DigitFab\TelegramBot\Contracts\UpdateInterface;

abstract class UpdateType
{
    const TYPE_CALLBACK_QUERY = 'callback_query';
    const TYPE_MESSAGE = 'message';

    protected $name;

    protected $data;

    public function __construct($name, $data)
    {
        $this->data = $data;
        $this->name = $name;
    }

    public static function make(UpdateInterface $update) {
        if ( ! empty($update->getData()['callback_query'])) {
            return new CallbackQuery(self::TYPE_CALLBACK_QUERY, $update->getData());
        } else {
            return new Message(self::TYPE_MESSAGE, $update->getData());
        }
    }

    public function getName() {
        return $this->name;
    }

    public function getChatId()
    {
        return $this->doGetChatId();
    }

    public function getMessage()
    {
        return $this->doGetMessage();
    }

    public function getRuleText()
    {
        return $this->doGetRuleText();
    }

    abstract protected function doGetChatId();
    abstract protected function doGetMessage();
    abstract protected function doGetRuleText();

}