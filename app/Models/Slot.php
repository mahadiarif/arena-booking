<?php

namespace App\Models;

use App\Enums\SlotStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Slot extends Model
{
    use HasFactory;

    protected $fillable = [
        'venue_id',
        'schedule_id',
        'date',
        'start_time',
        'end_time',
        'label',
        'status',
        'max_bookings',
        'current_bookings',
    ];

    protected function casts(): array
    {
        return [
            'date'   => 'date',
            'status' => SlotStatus::class,
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function activeBookings(): HasMany
    {
        return $this->hasMany(Booking::class)
                    ->whereNotIn('status', ['cancelled', 'no_show']);
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeAvailable($query): void
    {
        $query->whereIn('status', [SlotStatus::Available->value, SlotStatus::Partial->value])
              ->whereRaw('current_bookings < max_bookings');
    }

    public function scopeForDate($query, Carbon $date): void
    {
        $query->where('date', $date->toDateString());
    }

    public function scopeForVenue($query, int $venueId): void
    {
        $query->where('venue_id', $venueId);
    }

    // ── Accessors ──────────────────────────────────────────────────────────────

    public function getDurationMinutesAttribute(): int
    {
        return (int) Carbon::createFromTimeString($this->start_time)
                           ->diffInMinutes(Carbon::createFromTimeString($this->end_time));
    }

    public function getStartTimeFormattedAttribute(): string
    {
        return Carbon::createFromTimeString($this->start_time)->format('g:i A');
    }

    public function getEndTimeFormattedAttribute(): string
    {
        return Carbon::createFromTimeString($this->end_time)->format('g:i A');
    }
}
