<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * @method static orderBy(string $string)
 * @property false|mixed $has_password_request
 * @property mixed $password
 */
class Voter extends Model
{
    use HasFactory, Notifiable;

    /**
     * @var false|mixed
     */
    protected $fillable = [
        'student_number',
        'password',
        'email',
        'mobile',
        'college',
        'has_password_request',
        'slug',
    ];

    protected $casts = [
        'has_password_request' => 'boolean',
    ];
}
