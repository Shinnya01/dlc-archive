<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResearchProject extends Model
{
    public $fillable = [
        'title',
        'author',
        'year',
        'file',
    ];
}
