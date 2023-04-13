<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class College extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'dean_id'];

    public $timestamps = false;

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }
    public function dean()
    {
        return $this->belongsTo(User::class, 'dean_id');
    }
}
