<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UrlFilter extends Model
{
    protected $fillable = ['name', 'filters'];

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'url_filter_groups')
            ->withTimestamps();
    }

    public function devices()
    {
        return $this->belongsToMany(Device::class, 'url_filter_devices')
            ->withTimestamps();
    }
}
