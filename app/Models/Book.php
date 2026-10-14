<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    protected $fillable = [
        'author_id',
        'isbn',
        'title',
        'published_year',
        'is_reference',
        'cover_path',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class)
            ->withPivot('borrowed_at', 'returned_at')
            ->withTimestamps();
    }
}
