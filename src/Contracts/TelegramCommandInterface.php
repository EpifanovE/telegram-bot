<?php

namespace DigitFab\TelegramBot\Contracts;

interface TelegramCommandInterface
{
    public function getName(): string;

    public function getDescription(): string;

    public function handle(UpdateInterface $update);
}