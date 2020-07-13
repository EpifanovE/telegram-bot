<?php

namespace DigitFab\TelegramBot;

use DigitFab\TelegramBot\Contracts\RequestHandlerInterface;
use DigitFab\TelegramBot\Contracts\ResolverInterface;
use DigitFab\TelegramBot\Contracts\UpdateInterface;

class Bot
{
    private $config;

    private $update;

    private $resolver;

    /**
     * @var RequestHandlerInterface
     */
    private $requestHandler;

    public function __construct(
        array $config,
        UpdateInterface $update,
        ResolverInterface $resolver,
        RequestHandlerInterface $requestHandler
    ) {
        $this->config         = $config;
        $this->update         = $update;
        $this->resolver       = $resolver;
        $this->requestHandler = $requestHandler;
    }

    public function run()
    {
        if ($command = $this->resolver->getCommand()) {
            $command->handle($this->update);
        }
    }

    public function set()
    {
        return $this->requestHandler->setWebhook(
            [
                'url' => $this->config['webhookUrl'],
            ]
        );
    }

    public function deleteWebhook()
    {
        return $this->requestHandler->deleteWebhook();
    }

}