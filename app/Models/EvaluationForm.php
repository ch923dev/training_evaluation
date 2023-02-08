<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationForm extends Model
{
    use HasFactory;


    protected $fillable = [
        'activity_id',
        'evaluatorName',
        'remarks',
        'overall_rating',
    ];
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
    public function evaluation_answers()
    {
        return $this->hasMany(EvaluationAnswer::class);
    }
}
