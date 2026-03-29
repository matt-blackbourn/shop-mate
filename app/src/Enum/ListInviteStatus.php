<?php

namespace App\Enum;

enum ListInviteStatus: string
{
    case PENDING = 'pending'; 
    case ACCEPTED = 'accepted';  
    case DECLINED = 'declined';  
}


