<?php

namespace DigitFab\TelegramBot\Contracts;

interface UpdateInterface
{
    public function getData();

    public function getMessage();

    public function getRuleText();

    public function getChatId();

    public function isType($type): bool;

    public function getType();
}