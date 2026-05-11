<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ResourceGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'color',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function venues(): BelongsToMany
    {
        return $this->belongsToMany(Venue::class, 'resource_group_venue');
    }
}
