<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;

class Device extends Model
{
    protected $fillable = [
        'name',
        'mac_address',
        'allow_connection',
    ];

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_devices', 'device_id', 'group_id');
    }

    public function filters(): BelongsToMany
    {
        return $this->belongsToMany(UrlFilter::class, 'url_filter_devices', 'device_id', 'url_filter_id');
    }

    public function logs()
    {
        return $this->hasMany(DeviceLog::class);
    }

    protected static function boot()
    {
        parent::boot();
    }
}
