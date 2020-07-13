<?php

namespace DigitFab\TelegramBot\Classes;

class InlineKeyboard
{
    protected $rows = [];

    public function get()
    {
        return json_encode(['inline_keyboard' => $this->rows]);
    }

    public function addRow(array $buttons)
    {
        $this->rows[] = $buttons;
    }
}