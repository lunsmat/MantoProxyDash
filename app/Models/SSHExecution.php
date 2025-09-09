<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;

class SSHExecution extends Model
{
    use HasTimestamps;

    protected $table = 'ssh_executions';

    protected $fillable = [
        'status',
        'script_path',
        'command',
        'output',
        'object_type',
        'object_id',
        'user_id',
        'parent_id',
        'ssh_user_id',
    ];

    public function object()
    {
        return $this->morphTo();
    }

    public function systemLog()
    {
        return $this->morphMany(SystemLog::class, 'object');
    }

    public function parent()
    {
        return $this->belongsTo(SSHExecution::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(SSHExecution::class, 'parent_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sshUser()
    {
        return $this->belongsTo(SSHUser::class, 'ssh_user_id');
    }

    public function getSystemIdentifierAttribute(): string
    {
        return 'Execução SSH: ' . $this->id;
    }

    public function getObjectIdentifierAttribute()
    {
        if ($this->object_id !== null && $this->object === null) $this->load('object');
        if ($this->object) return $this->object->system_identifier ?? (string) $this->object_id;
        if ($this->object_id !== null) return (string) $this->object_type . '-' . $this->object_id;

        return 'N/A';
    }

    public function getRunTypeAttribute(): string
    {
        return $this->script_path ? 'script' : 'command';
    }
}
