<?php

namespace DigitFab\TelegramBot\Contracts;

interface HttpClientInterface
{
    public function post($url, $params = []);

    public function sendMultipart($url, $params);
}