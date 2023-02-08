<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_form_id',
        'question_id',
        'answer',
    ];

    public function evaluationForm()
    {
        return $this->belongsTo(EvaluationForm::class);
    }
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
