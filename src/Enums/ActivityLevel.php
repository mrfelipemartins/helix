<?php

namespace MrFelipeMartins\Helix\Enums;

enum ActivityLevel: string
{
    case INFO = 'info';
    case WARN = 'warn';
    case ERROR = 'error';

    public function variant(): string
    {
        return match ($this) {
            self::INFO => 'slate',
            self::WARN => 'emerald',
            self::ERROR => 'amber',
        };
    }
}
