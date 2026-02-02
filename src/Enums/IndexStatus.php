<?php

namespace MrFelipeMartins\Helix\Enums;

enum IndexStatus: string
{
    case READY = 'ready';
    case OPTIMIZING = 'optimizing';
    case ERROR = 'error';

    public function variant(): string
    {
        return match ($this) {
            self::READY => 'emerald',
            self::OPTIMIZING => 'amber',
            self::ERROR => 'rose',
        };
    }
}
