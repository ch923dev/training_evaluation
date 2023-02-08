<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = [
        'section_id',
        'question',
        'type' ,
    ];
    public function section()
    {
        return $this->belongsTo(Section::class);
    }
    public function evaluation_answers() {
        return $this->hasMany(EvaluationAnswer::class);
    }
}
