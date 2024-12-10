<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'bio', 'birth_date'];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public function scopeName($query, $name)
    {
        return $query->where('name', 'LIKE', "%$name%");
    }
}
