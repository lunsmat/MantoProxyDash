<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SSHUser extends Model
{
    protected $table = 'ssh_users';

    protected $fillable = [
        'port',
        'username',
        'authentication_method',
        'password',
        'public_key_file_path',
        'private_key_file_path',
        'passphrase',
    ];

    protected $hidden = [
        'password',
        'private_key_file_path',
        'passphrase',
    ];

    protected $casts = [
        'port' => 'integer',
        'password' => 'encrypted',
        'passphrase' => 'encrypted',
    ];
}
