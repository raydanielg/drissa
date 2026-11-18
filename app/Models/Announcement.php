<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'title', 'body', 'type', 'action_label', 'action_url',
        'dismissible', 'is_active', 'priority', 'starts_at', 'ends_at', 'created_by',
    ];

    protected $casts = [
        'dismissible' => 'boolean',
        'is_active' => 'boolean',
        'priority' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public const TYPES = ['info', 'success', 'warning', 'critical'];

    public function scopeActive($query)
    {
        $now = now();

        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now));
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
