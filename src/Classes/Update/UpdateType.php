<?php

namespace DigitFab\TelegramBot\Classes\Update;

abstract class UpdateType
{
    protected $name;

    protected $data;

    public function __construct($name, $data)
    {
        $this->data = $data;
        $this->name = $name;
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

    public function getContent()
    {
        return $this->doGetContent();
    }

    abstract protected function doGetChatId();
    abstract protected function doGetMessage();
    abstract protected function doGetContent();

}