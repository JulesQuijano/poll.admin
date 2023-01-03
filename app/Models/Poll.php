<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $status
 */
class Poll extends Model
{
    use HasFactory;

    const Pending ='Pending';
    const Ongoing = 'Ongoing';
    const Concluded = 'Concluded';
    const Maintenance = 'Maintenance';

    const STATUS_PENDING = 1;
    const STATUS_ONGOING = 2;
    const STATUS_CONCLUDED = 3;
    const STATUS_MAINTENANCE = 4;

    const College = 'College';
    const University = 'University';

    const CATEGORY_COLLEGE = 1;
    const CATEGORY_UNIVERSITY = 2;

    protected $fillable = [
        'title',
        'description',
        'start',
        'duration',
        'status',
        'category',
    ];

    protected $casts = [
        'start' => 'date'
    ];

    public function nominees()
    {
        return $this->hasMany(Nominee::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function canDelete()
    {
        return ($this->nominees->count() === 0) or ($this->votes->count()===0);
    }

    public function canEdit()
    {
        return ($this->status !== self::STATUS_CONCLUDED);
    }
    /**
     * @param $status
     * @return string
     */
    public static function strStatus($status)
    {
        // implementation: App\Models\Poll::strStatus($poll->status)
        return match ($status) {
            1 => self::Pending,
            2 => self::Ongoing,
            3 => self::Concluded,
            4 => self::Maintenance,
        };
    }

    public function statusStr()
    {
        return match ($this->status) {
            self::STATUS_PENDING => self::Pending,
            self::STATUS_ONGOING => self::Ongoing,
            self::STATUS_CONCLUDED => self::Concluded,
            self::STATUS_MAINTENANCE => self::Maintenance,
        };
    }

    public function categoryStr()
    {
        return match ($this->category) {
            self::CATEGORY_COLLEGE => self::College,
            self::CATEGORY_UNIVERSITY => self::University,
        };
    }

}
