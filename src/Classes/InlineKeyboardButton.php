<?php

namespace DigitFab\TelegramBot\Classes;

class InlineKeyboardButton implements \JsonSerializable
{
    protected $text;

    protected $params;

    public function __construct($text, $params)
    {
        $this->text = $text;
        $this->params = $params;
    }

    public static function make($text, $params) {
        return new self($text, $params);
    }

    public function jsonSerialize()
    {
        return array_merge([
            'text' => $this->text,
        ], $this->params);
    }
}