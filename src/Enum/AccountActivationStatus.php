<?php

namespace App\Enum;

enum AccountActivationStatus
{
    case Activated;
    case AlreadyActivated;
    case Expired;
    case Invalid;
}
