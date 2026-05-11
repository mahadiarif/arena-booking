<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlackoutPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'schedule_id',
        'venue_id',
        'start_datetime',
        'end_datetime',
        'repeats_annually',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_datetime'   => 'datetime',
            'end_datetime'     => 'datetime',
            'repeats_annually' => 'boolean',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeActiveOn($query, Carbon $date): void
    {
        $query->where('start_datetime', '<=', $date)
              ->where('end_datetime', '>=', $date);
    }
}
