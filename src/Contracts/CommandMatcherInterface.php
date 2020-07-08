<?php

namespace DigitFab\TelegramBot\Contracts;

interface CommandMatcherInterface
{
    public function match(UpdateInterface $update): bool;
}