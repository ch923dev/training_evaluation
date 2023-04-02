<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'venue', 'facilitator', 'date', 'key'];

    public function evaluation_forms()
    {
        return $this->hasMany(EvaluationForm::class);
    }
    public function sections()
    {
        return $this->hasMany(Section::class);
    }
    public function questions()
    {
        return $this->hasManyThrough(Question::class, Section::class);
    }
}
