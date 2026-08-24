<?php

namespace App\Enums;

enum UserPrivilege: string
{
    case GUEST = 'guest';
    case MEMBER = 'member';
    case MUSIC = 'music';
    case ELDER = 'elder';
    case ADMIN = 'admin';
}