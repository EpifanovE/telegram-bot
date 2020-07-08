<?php

namespace DigitFab\TelegramBot\Classes;

use DigitFab\TelegramBot\Contracts\HttpClientInterface;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class GuzzleHttpClient implements HttpClientInterface
{
    /**
     * @var Client
     */
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function post($url, $params = [])
    {
        return $this->client->post(
            $url,
            [
                RequestOptions::HEADERS => [
                    'Content-Type' => 'application/json',
                ],
                RequestOptions::JSON => $params
            ]
        );
    }

    public function sendMultipart($url, $params) {
        return $this->client->post(
            $url,
            [
                RequestOptions::MULTIPART => $this->multipartParamsMap($params),
            ]
        );
    }

    private function multipartParamsMap($params)
    {
        return array_map(
            function ($key, $value) {
                return [
                    'name' => $key,
                    'contents' => $value
                ];
            },
            array_keys($params),
            $params
        );
    }
}