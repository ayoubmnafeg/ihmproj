<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['text', 'publication_id', 'parent_id'])]
class Comment extends Content
{
    protected $table = 'comments';

    // Join with contents to get shared fields
    public function newQuery()
    {
        return parent::newQuery()->join(
            'contents',
            'contents.id',
            '=',
            'comments.id'
        )->select('comments.*', 'contents.status', 'contents.author_id', 'contents.created_at', 'contents.updated_at');
    }

    public function resolveRouteBinding($value, $field = null): ?static
    {
        return $this->where('comments.id', $value)->first();
    }

    public function publication(): BelongsTo
    {
        return $this->belongsTo(Publication::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}
