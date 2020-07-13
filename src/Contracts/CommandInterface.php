<?php

namespace DigitFab\TelegramBot\Contracts;

interface CommandInterface
{
    public function handle(UpdateInterface $update);

    public function setUpdate(UpdateInterface $update);

    public function setRequestHandler(RequestHandlerInterface $requestHandler);

    public function setAttributes(array $attributes = []);
}