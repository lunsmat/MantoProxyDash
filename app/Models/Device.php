<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;

class Device extends Model
{
    use HasFactory;

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

    public function sshExecutions()
    {
        return $this->morphMany(SSHExecution::class, 'object');
    }

    public function sshDefaultUser()
    {
        return $this->belongsTo(SSHUser::class, 'default_ssh_user');
    }

    protected static function boot()
    {
        parent::boot();
    }

    public function systemLog()
    {
        return $this->morphMany(SystemLog::class, 'object');
    }

    public function getSystemIdentifierAttribute(): string
    {
        return 'Dispositivo: ' . $this->id . ' - ' . $this->name . ' (' . $this->mac_address . ')';
    }
}
