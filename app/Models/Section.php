<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'title',
    ];
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
    public function questions() {
        return $this->hasMany(Question::class);
    }
}

