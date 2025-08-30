<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'description',
    ];

    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(Device::class, 'group_devices', 'group_id', 'device_id');
    }

    public function filters(): BelongsToMany
    {
        return $this->belongsToMany(UrlFilter::class, 'url_filter_groups', 'group_id', 'url_filter_id');
    }

    public function systemLog()
    {
        return $this->morphMany(SystemLog::class, 'object');
    }

    public function getSystemIdentifierAttribute(): string
    {
        return 'Grupo: ' . $this->id . ' - ' . $this->name;
    }
}
