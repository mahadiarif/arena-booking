<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class VenueImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'venue_id', 'path', 'alt_text', 'is_primary', 'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = ['url'];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }
}
