<?php

namespace DigitFab\TelegramBot\Commands;

use DigitFab\TelegramBot\Classes\CommandsManager;
use DigitFab\TelegramBot\Contracts\TelegramCommandInterface;
use DigitFab\TelegramBot\Contracts\UpdateInterface;

class HelpCommand extends BaseCommand implements TelegramCommandInterface
{
    private $commandsManager;

    public function __construct(CommandsManager $commandsManager)
    {
        $this->commandsManager = $commandsManager;
    }

    public function getName(): string
    {
        return 'help';
    }

    public function getDescription(): string
    {
        return 'Помощь';
    }

    public function handle(UpdateInterface $update)
    {
        $this->sendPhoto(
            [
//                'photo' => 'https://yandex.ru/collections/card/5e496d8cd497a8e0096a0dc2/',
                'photo' => '/var/www/html/assets/example.jpg',
                'caption' => 'Test caption',
                'reply_markup' => json_encode(
                    [
                        'inline_keyboard' => [
                            [
                                [
                                    'text' => 'Button 1',
                                    'callback_data' => '/start'
                                ],
                                [
                                    'text' => 'Button 2',
                                    'callback_data' => '/start'
                                ],
                            ],
                            [
                                [
                                    'text' => 'Button 3',
                                    'callback_data' => '/start'
                                ],
                            ],
                        ],
                    ]
                ),
            ]
        );

//        $text = join(PHP_EOL, array_map(function ($command) {
//            /**
//             * @var TelegramCommandInterface $command
//             */
//            return sprintf('/%s - %s', $command->getName(), $command->getDescription());
//        }, array_filter($this->commandsManager->getCommands(), function ($command) {
//            return $command instanceof TelegramCommandInterface;
//        })));
//
//        $this->sendMessage(
//            [
//                'text' => $text,
//                'reply_markup' => json_encode([
//                    'inline_keyboard' => [
//                        [
//                            [
//                                'text' => 'Button 1',
//                                'callback_data' => '/start'
//                            ],
//                            [
//                                'text' => 'Button 2',
//                                'callback_data' => '/start'
//                            ],
//                        ],
//                        [
//                            [
//                                'text' => 'Button 3',
//                                'callback_data' => '/start'
//                            ],
//                        ],
//                    ],
//                ]),
//            ]
//        );
    }

}