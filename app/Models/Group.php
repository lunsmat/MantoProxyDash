<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
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
}
