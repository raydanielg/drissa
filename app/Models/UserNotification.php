<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    protected $fillable = ['user_id', 'title', 'body', 'type', 'url', 'read_at'];

    protected $casts = ['read_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Push a notification to a set of user IDs (or all staff when $userIds is null).
     */
    public static function sendTo(array $data, $userIds = null): void
    {
        $ids = $userIds ?? User::query()->pluck('id');
        $now = now();
        $rows = collect($ids)->map(fn ($id) => [
            'user_id' => $id,
            'title' => $data['title'],
            'body' => $data['body'] ?? null,
            'type' => $data['type'] ?? 'info',
            'url' => $data['url'] ?? null,
            'created_at' => $now,
            'updated_at' => $now,
        ])->all();

        static::insert($rows);
    }
}
