<?php

namespace DigitFab\TelegramBot\Classes\Update;

class Message extends UpdateType
{
    public function doGetChatId()
    {
        return $this->data['message']['chat']['id'];
    }

    public function doGetMessage()
    {
        return $this->data['message'];
    }

    public function doGetContent()
    {
        return $this->data['message']['text'];
    }
}