<?php

namespace DigitFab\TelegramBot\Classes;

use DigitFab\TelegramBot\Contracts\CommandInterface;
use DigitFab\TelegramBot\Contracts\RequestHandlerInterface;
use DigitFab\TelegramBot\Contracts\UpdateInterface;
use DigitFab\TelegramBot\Exceptions\TelegramBotException;

class CommandsManager
{
    private $update;

    private $rules;

    private $requestHandler;

    public function __construct(
        UpdateInterface $update,
        RequestHandlerInterface $requestHandler
    ) {
        $this->update         = $update;
        $this->requestHandler = $requestHandler;
    }

    public function getRules() {
        return $this->rules;
    }

    public function getCommands() {
        return array_map(function ($rule) {
            /**
             * @var CommandRule $rule
             */
            return $this->getCommandObject($rule->getCommand());
        }, $this->rules);
    }

    public function getCommandObject($command): CommandInterface
    {
        if (is_callable($command)) {
            $result = $command($this);

            if ($this->checkCommandObject($result)) {
                /**
                 * @var CommandInterface $result
                 */
                return $this->setCommandObjectProps($result);
            }
        }

        if ($this->checkCommandObject($command)) {
            /**
             * @var CommandInterface $command
             */
            return $this->setCommandObjectProps($command);
        }

        if (is_string($command) && class_exists($command)) {
            $result = new $command;

            if ($this->checkCommandObject($result)) {
                return $this->setCommandObjectProps($result);
            }
        }

        throw new TelegramBotException('Command is invalid.');
    }

    public function addRules($rules) {
        $this->rules = $rules;
    }

    private function checkCommandObject($object)
    {
        return is_object($object) && $object instanceof CommandInterface;
    }

    private function setCommandObjectProps(CommandInterface $command)
    {
        $command->setUpdate($this->update);
        $command->setRequestHandler($this->requestHandler);
        $command->setAttributes($this->getAttributes());

        return $command;
    }

    private function getAttributes() {
        $array = explode(' ', $this->update->getRuleText());
        array_shift($array);
        return $array;
    }
}