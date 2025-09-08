<?php

namespace App\Enums;

enum IdentityType: string
{
    case CC = 'CC';
    case CE = 'CE';
    case NIT = 'NIT';
    case Passport = 'Passport';
}