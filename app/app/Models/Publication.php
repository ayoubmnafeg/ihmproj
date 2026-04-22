<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'text', 'media_type', 'category_id'])]
class Publication extends Content
{
    protected $table = 'publications';

    // Join with contents to get shared fields
    public function newQuery()
    {
        return parent::newQuery()->join(
            'contents',
            'contents.id',
            '=',
            'publications.id'
        )->select('publications.*', 'contents.status', 'contents.author_id', 'contents.created_at', 'contents.updated_at');
    }

    public function resolveRouteBinding($value, $field = null): ?static
    {
        return $this->where('publications.id', $value)->first();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(MediaAttachment::class);
    }
}
