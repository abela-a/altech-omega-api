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

    public function scopeSearch($query, $filter)
    {
        $query->when($filter ?? null, function ($query, $search) {
            $query->where('name', 'LIKE', '%'.$search.'%');
        });
    }
}
