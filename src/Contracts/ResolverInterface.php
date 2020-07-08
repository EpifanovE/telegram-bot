<?php

namespace DigitFab\TelegramBot\Contracts;

interface ResolverInterface
{
    public function getCommand(): CommandInterface;
}