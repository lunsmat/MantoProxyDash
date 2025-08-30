<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupDeactivation extends Model
{
    protected $fillable = [
        'user_id',
        'group_id',
        'deactivation_datetime',
        'reactivation_datetime',
        'reason',
        'deactivation_occurred',
        'reactivation_occurred',
    ];

    protected $casts = [
        'deactivation_datetime' => 'datetime',
        'reactivation_datetime' => 'datetime',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $appends = [
    ];

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function systemLog()
    {
        return $this->morphMany(SystemLog::class, 'object');
    }

    public function getDeactivationTimeFormattedAttribute(): string
    {
        return $this->deactivation_datetime?->format('d/m/Y H:i') ?? '';
    }

    public function getReactivationTimeFormattedAttribute(): string
    {
        return $this->reactivation_datetime?->format('d/m/Y H:i') ?? '';
    }
}
