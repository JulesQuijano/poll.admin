<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    public function nominee()
    {
        return $this->belongsTo(Nominee::class);
    }
}
