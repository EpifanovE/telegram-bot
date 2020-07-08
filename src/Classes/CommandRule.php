<?php

namespace DigitFab\TelegramBot\Classes;

class CommandRule
{
    private $matcher;

    private $command;

    public static function make($matcher, $command) {
        return new self($matcher, $command);
    }

    public function __construct($matcher, $command)
    {
        $this->matcher = $matcher;
        $this->command = $command;
    }

    public function getMatcher()
    {
        return $this->matcher;
    }

    public function getCommand()
    {
        return $this->command;
    }

}