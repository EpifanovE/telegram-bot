<?php

namespace DigitFab\TelegramBot\Commands;

use DigitFab\TelegramBot\Contracts\UpdateInterface;

class DownloadCommand extends BaseCommand
{
    public function handle(UpdateInterface $update)
    {
        $fileName = $this->saveFileByUpdate($update);
    }
}