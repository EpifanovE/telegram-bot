<?php

namespace DigitFab\TelegramBot\Classes\Update;

class CallbackQuery extends UpdateType
{
    public function doGetChatId()
    {
        return $this->data['callback_query']['message']['chat']['id'];
    }

    public function doGetMessage()
    {
        return $this->data['callback_query']['message'];
    }

    public function doGetContent()
    {
        return $this->data['callback_query']['data'];
    }
}