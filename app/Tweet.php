<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Tweet extends Model
{
    protected $fillable = [
        'text',
        'image',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function getSafeImageUrlAttribute(): ?string
    {
        $image = $this->image;

        if (! is_string($image) || $image === '' || filter_var($image, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        $scheme = strtolower((string) parse_url($image, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https'], true) ? $image : null;
    }
}
