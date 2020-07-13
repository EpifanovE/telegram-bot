<?php

namespace DigitFab\TelegramBot\Classes;

class Keyboard
{
    private $params;

    protected $rows = [];

    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    public function get()
    {
        return json_encode(array_merge(['keyboard' => $this->rows], $this->params));
    }

    public function addRow(array $buttons)
    {
        $this->rows[] = $buttons;
    }
}