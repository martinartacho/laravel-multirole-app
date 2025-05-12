<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'sender_id',
        'recipient_type',
        'recipient_role',
        'recipient_ids',
        'is_published',
        'published_at',
        'email_sent',
        'web_sent',
        'push_sent'
    ];

    protected $casts = [
        'recipient_ids' => 'array',
        'published_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
    
    public function recipients()
    {
        return $this->belongsToMany(User::class, 'notification_user')
                    ->withPivot(['read', 'read_at'])
                    ->orderBy('notification_user.created_at', 'desc')
                    ->withTimestamps();
    }

    public function scopeUnread($query, $userId)
    {
        return $query->whereHas('recipients', function($q) use ($userId) {
            $q->where('user_id', $userId)
              ->where('read', false)
              ->orderBy('notification_user.created_at', 'desc');
        });
    }

    public function markAsRead($userId)
    {
        $this->recipients()->updateExistingPivot($userId, [
            'read' => true,
            'read_at' => now()
        ]);
    }


}