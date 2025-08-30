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

    public function systemLog()
    {
        return $this->morphMany(SystemLog::class, 'object');
    }

    public function getSystemIdentifierAttribute(): string
    {
        return 'Log de Requisiçaõ: ' . $this->id . ' - ' . $this->http_url . ' (' . $this->http_method . ')';
    }
}
