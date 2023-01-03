<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property mixed $role
 * @property mixed $status
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_SUPER_ADMIN = 1;
    const ROLE_ADMIN = 2;
    const ROLE_EMAIL_NOTIFICATION = 3;

    const SuperAdmin = 'Super Admin';
    const Admin = 'Admin';
    const EmailNotification = 'Email Notification';

    const STATUS_INACTIVE=0;
    const STATUS_ACTIVE=1;

    const Inactive = 'Inactive';
    const Active = 'Active';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'slug',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return string
     */
    public function roleStr(): string
    {
        return match($this->role) {
            self::ROLE_SUPER_ADMIN => self::SuperAdmin,
            self::ROLE_ADMIN => self::Admin,
            self::ROLE_EMAIL_NOTIFICATION => self::EmailNotification,
        };
    }

    /**
     * @return string
     */
    public function statusStr(): string
    {
        return match($this->status) {
            self::STATUS_INACTIVE => self::Inactive,
            self::STATUS_ACTIVE => self::Active,
        };
    }

    /**
     * @return bool
     */
    public function canModify(): bool
    {
        return !($this->role === self::ROLE_SUPER_ADMIN || $this->role === self::ROLE_EMAIL_NOTIFICATION);
    }

    public function isSuper()
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }
}
