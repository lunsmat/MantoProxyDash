<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    protected $fillable = [
        'message',
        'context',
        'user_id',
        'object_id',
        'object_type',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    protected $hidden = [
        'user_id',
        'object_id',
        'object_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function object()
    {
        return $this->morphTo();
    }

    public function getObjectIdentifierAttribute()
    {
        if ($this->object_id !== null && $this->object === null) $this->load('object');
        if ($this->object) return $this->object->system_identifier ?? (string) $this->object_id;
        if ($this->object_id !== null) return (string) $this->object_type . '-' . $this->object_id;

        return 'N/A';
    }

    public function getUserIdentifierAttribute()
    {
        if ($this->user_id !== null && $this->user === null) $this->load('user');
        if ($this->user) return $this->user->system_identifier ?? (string) $this->user_id;
        if ($this->user_id !== null) return (string) $this->user_id;

        return 'N/A';
    }

    public function getSystemIdentifierAttribute()
    {
        return 'Log: ' . $this->id ;
    }

    public function toArray()
    {
        $data = parent::toArray();

        $data['system_identifier'] = $this->system_identifier;
        $data['user_identifier'] = $this->user_identifier;
        $data['object_identifier'] = $this->object_identifier;

        return $data;
    }
}
