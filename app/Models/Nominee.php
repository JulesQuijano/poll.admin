<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nominee extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_id',
        'name',
        'position_id',
        'affiliation',
        'slug',
    ];

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function getPosition()
    {
        return Position::find($this->position_id);
    }

    public static function hasPosition($position): bool
    {
        return self::query()->where('position_id',$position)->get()->count() > 0;
    }
}
