<?php

namespace App\Enums;

enum BotChannel: string
{
    case Discord = 'discord';
    case Line = 'line';
    case Telegram = 'telegram';

    public function label(): string
    {
        return match($this) {
            self::Discord => 'Discord',
            self::Line => 'LINE',
            self::Telegram => 'Telegram',
        };
    }
}
