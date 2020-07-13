<?php

namespace DigitFab\TelegramBot\Contracts;

use Psr\Http\Message\ResponseInterface;

interface HttpClientInterface
{
    public function post($url, $params = []) : ResponseInterface;

    public function get($url, $params = []): ResponseInterface;

    public function download($url, $to): ResponseInterface;
}