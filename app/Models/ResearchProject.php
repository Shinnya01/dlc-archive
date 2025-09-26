<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResearchProject extends Model
{
    protected $fillable = [
        'title',
        'author',
        'year',
        'file',
    ];

    protected $casts = [
    'author'   => 'array',
    'keywords' => 'array',
    ];

}
