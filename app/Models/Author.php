<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $fillable = ['name', 'bio', 'birth_date'];

    protected $casts = [
        'birth_date' => 'date',
    ];
}
