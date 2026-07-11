<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'created_by', 'type'];

    protected $casts = [
        'type' => 'string',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'conversation_user')
            ->withPivot('last_read_at')
            ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    public function unreadCountFor(User $user): int
    {
        $lastRead = $this->users()
            ->where('user_id', $user->id)
            ->first()?->pivot?->last_read_at;

        return $this->messages()
            ->where('user_id', '!=', $user->id)
            ->when($lastRead, fn($q) => $q->where('created_at', '>', $lastRead))
            ->count();
    }

    public function otherUser(User $user): ?User
    {
        if ($this->type !== 'private') return null;
        return $this->users()->where('users.id', '!=', $user->id)->first();
    }

    public function titleFor(User $user): string
    {
        if ($this->title) return $this->title;
        if ($this->type === 'private') {
            return $this->otherUser($user)?->name ?? 'Private Chat';
        }
        return 'Group Chat';
    }
}
