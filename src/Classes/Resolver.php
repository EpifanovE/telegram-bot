<?php

namespace DigitFab\TelegramBot\Classes;

use DigitFab\TelegramBot\Contracts\CommandInterface;
use DigitFab\TelegramBot\Contracts\CommandMatcherInterface;
use DigitFab\TelegramBot\Contracts\ResolverInterface;
use DigitFab\TelegramBot\Contracts\UpdateInterface;
use DigitFab\TelegramBot\Exceptions\CommandNotFoundException;
use DigitFab\TelegramBot\Exceptions\TelegramBotException;

class Resolver implements ResolverInterface
{
    private $update;

    private $commandsManager;

    public function __construct(
        UpdateInterface $update,
        CommandsManager $commandsManager
    ) {
        $this->update          = $update;
        $this->commandsManager = $commandsManager;
    }

    /**
     * @return CommandInterface
     * @throws CommandNotFoundException
     * @throws TelegramBotException
     */
    public function getCommand(): CommandInterface
    {
        foreach ($this->commandsManager->getRules() as $rule) {
            /**
             * @var CommandRule $rule
             */

            if ($this->isMatch($rule->getMatcher())) {
                return $this->commandsManager->getCommandObject($rule->getCommand());
            }
        }

        throw new CommandNotFoundException('Commands not found.');
    }

    private function isMatch($matcher): bool
    {
        if (is_string($matcher)) {
            if (class_exists($matcher)) {
                /**
                 * @var CommandMatcherInterface $matcherObject
                 */
                $matcherObject = new $matcher;

                return $matcherObject->match($this->update);
            }

            if ( ! empty($this->update->getContent()) && (substr($matcher, 0, 1) === '/')) {
                return $this->update->getContent() === $matcher;
            }
        }

        if (is_object($matcher)) {
            /**
             * @var CommandMatcherInterface $matcher
             */
            return $matcher->match($this->update);
        }

        return false;
    }
}