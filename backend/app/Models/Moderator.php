<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Moderator extends Model
{
    protected $primaryKey = 'user_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['user_id', 'assigned_since'];

    protected function casts(): array
    {
        return [
            'assigned_since' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
