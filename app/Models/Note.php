<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'content',
        'note_date',
        'is_favorite',
    ];

    protected $casts = [
        'note_date' => 'date',
        'is_favorite' => 'boolean',
    ];

    /**
     * Get the user that owns the note.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category of the note.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
