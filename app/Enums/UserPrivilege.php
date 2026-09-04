<?php

namespace App\Enums;

enum UserPrivilege: string
{
    case GUEST = 'guest';
    case USER = 'user';
    case MUSIC = 'music';
    case MEMBER = 'member';
    case ELDER = 'elder';
    case ADMIN = 'admin';
}