<?php

namespace DigitFab\TelegramBot\Classes;

use DigitFab\TelegramBot\Contracts\HttpClientInterface;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

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

    public function post($url, $params = []): ResponseInterface
    {
        return $this->client->post(
            $url,
            [
                RequestOptions::MULTIPART => ! empty($params) ? $this->multipartParamsPrepare($params) : [],
            ]
        );
    }

    public function get($url, $params = []): ResponseInterface
    {
        return $this->client->get($url, ['query' => $params]);
    }

    public function download($url, $to): ResponseInterface
    {
        return $this->client->request('GET', $url, ['sink' => $to]);
    }

    private function multipartParamsPrepare($params)
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