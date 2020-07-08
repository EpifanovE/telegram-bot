<?php

namespace DigitFab\TelegramBot;

use DigitFab\TelegramBot\Contracts\ResolverInterface;
use DigitFab\TelegramBot\Contracts\UpdateInterface;

class Bot
{
    private $config;

    private $update;

    private $resolver;

    public function __construct(
        array $config,
        UpdateInterface $update,
        ResolverInterface $resolver
    )
    {
        $this->config = $config;
        $this->update = $update;
        $this->resolver = $resolver;
    }

    public function run() {
        if ($command = $this->resolver->getCommand()) {
            $command->handle($this->update);
        }
    }

}