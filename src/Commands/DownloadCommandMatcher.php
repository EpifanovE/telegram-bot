<?php

namespace DigitFab\TelegramBot\Commands;

use DigitFab\TelegramBot\Contracts\CommandMatcherInterface;
use DigitFab\TelegramBot\Contracts\UpdateInterface;

class DownloadCommandMatcher implements CommandMatcherInterface
{
    private $fileTypes = [
        'photo',
        'audio',
        'document',
        'video',
        'animation',
        'video_note',
        'voice',
        'sticker',
    ];

    public function match(UpdateInterface $update): bool
    {
        $message = $update->getMessage();

        $fileType = null;

        foreach ($this->fileTypes as $type) {
            if (!empty($message[$type])) {
                $fileType = $type;
            }
        }

        if (is_null($fileType)) {
            return false;
        }

        if ( ! empty($message[$fileType])) {
            return true;
        }

        return false;
    }
}