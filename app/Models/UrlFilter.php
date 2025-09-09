<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UrlFilter extends Model
{
    use HasFactory;

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

    public function systemLog()
    {
        return $this->morphMany(SystemLog::class, 'object');
    }

    public function getSystemIdentifierAttribute(): string
    {
        return 'Filtro de URL: ' . $this->id . ' - ' . $this->name;
    }
}
