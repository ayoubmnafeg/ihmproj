<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['total_publications', 'total_reports', 'total_responses', 'recorded_at'])]
class PlatformStatistics extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'recorded_at' => 'datetime',
        ];
    }
}
