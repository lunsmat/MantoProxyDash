<?php

namespace App\Enums;

enum SSHAuthenticationType: string
{
    case UsernameAndPassword = 'username_and_password';
    case PublicKey = 'public_key';
}
