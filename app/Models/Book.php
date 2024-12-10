<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'publish_date', 'author_id'];

    protected $casts = [
        'publish_date' => 'date',
    ];

    public function author()
    {
        return $this->belongsTo(Author::class)->withDefault();
    }

    public function scopeSearch($query, $filter)
    {
        $query->when($filter ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'LIKE', '%'.$search.'%')
                    ->orWhere('description', 'LIKE', '%'.$search.'%');
            });
        });
    }

    public function scopePublishedAt($query, $date)
    {
        $query->when($date ?? null, fn ($query, $date) => $query->where('publish_date', $date));
    }
}
