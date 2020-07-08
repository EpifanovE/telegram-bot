<?php

namespace DigitFab\TelegramBot\Classes\Request;

class Image extends Request
{
    private $caption;

    public function __construct()
    {

    }

    public function setCaption($caption) {
        $this->caption = $caption;
    }
}