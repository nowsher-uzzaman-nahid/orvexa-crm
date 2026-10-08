<?php

namespace App\Enums;

enum DealStatus: string
{
    case OPEN = 'open';
    case WON = 'won';
    case LOST = 'lost';
}
