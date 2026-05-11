<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WaitlistEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'slot_id', 'customer_id', 'position', 'notified_at', 'expires_at',
    ];

    protected $casts = [
        'notified_at' => 'datetime',
        'expires_at'  => 'datetime',
        'position'    => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function slot(): BelongsTo
    {
        return $this->belongsTo(Slot::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function wasNotified(): bool
    {
        return $this->notified_at !== null;
    }
}
