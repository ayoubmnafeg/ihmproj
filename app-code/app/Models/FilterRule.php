<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['rule', 'for_label_moderation', 'is_enabled'])]
class FilterRule extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'for_label_moderation' => 'boolean',
            'is_enabled'           => 'boolean',
        ];
    }
}
