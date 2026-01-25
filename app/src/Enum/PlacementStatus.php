<?php

namespace App\Enum;

enum PlacementStatus: string
{
    case SYSTEM = 'system';
    case APPROX = 'approx';
    case MISSING = 'missing';
}
