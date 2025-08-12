<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceLog extends Model
{
    protected $fillable = [
        'device_id',
        'http_method',
        'http_url',
        'http_headers',
        'http_body',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
