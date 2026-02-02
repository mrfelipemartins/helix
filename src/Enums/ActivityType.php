<?php

namespace MrFelipeMartins\Helix\Enums;

enum ActivityType: string
{
    case CREATE = 'create';
    case DROP = 'drop';
    case INSERT = 'insert';
    case DELETE = 'delete';
    case SEARCH = 'search';
    case MAINTENANCE = 'maintenance';
}
