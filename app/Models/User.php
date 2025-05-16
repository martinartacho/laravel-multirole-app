<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable,  HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'locale',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

     /**
     * Get the attributes that should be cast.
     *
     * @return $with roles
     */
    /*

    public function getRoleNames()
    {
        return cache()->remember("user_{$this->id}_roles", 3600, function() {
            return $this->getRoleNames();
        });
    } */


    public function notifications()
    {
        return $this->belongsToMany(Notification::class, 'notification_user')
                    ->withPivot('read', 'read_at')
                    ->orderBy('created_at', 'desc');
    }
    
    
    public function unreadNotifications()
    {
        return $this->notifications()->wherePivot('read', false);
    }
    
}
