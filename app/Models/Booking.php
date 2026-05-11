<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Booking extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'booking_ref',
        'customer_id',
        'venue_id',
        'slot_id',
        'booked_by',
        'status',
        'total_amount',
        'paid_amount',
        'notes',
        'approved_by',
        'approved_at',
        'cancelled_by',
        'cancelled_at',
        'cancel_reason',
        'parent_booking_id',
        'recurrence_id',
        'check_in_at',
        'check_out_at',
    ];

    protected function casts(): array
    {
        return [
            'status'       => BookingStatus::class,
            'total_amount' => 'decimal:2',
            'paid_amount'  => 'decimal:2',
            'approved_at'  => 'datetime',
            'cancelled_at' => 'datetime',
            'check_in_at'  => 'datetime',
            'check_out_at' => 'datetime',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(Slot::class);
    }

    public function bookedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'booked_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(BookingParticipant::class);
    }

    public function attributeValues(): HasMany
    {
        return $this->hasMany(BookingAttributeValue::class);
    }

    public function parentBooking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'parent_booking_id');
    }

    public function childBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'parent_booking_id');
    }

    public function recurrenceRule(): BelongsTo
    {
        return $this->belongsTo(RecurrenceRule::class, 'recurrence_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeActive($query): void
    {
        $query->whereNotIn('status', [
            BookingStatus::Cancelled->value,
            BookingStatus::NoShow->value,
        ]);
    }

    public function scopeWithDue($query): void
    {
        $query->whereRaw('paid_amount < total_amount');
    }

    public function scopeToday($query): void
    {
        $query->whereHas('slot', fn ($q) => $q->where('date', now()->toDateString()));
    }

    // ── Accessors ──────────────────────────────────────────────────────────────

    public function getIsPaidAttribute(): bool
    {
        return (float) $this->paid_amount >= (float) $this->total_amount;
    }

    public function getDueAmountAttribute(): float
    {
        return max(0, (float) $this->total_amount - (float) $this->paid_amount);
    }

    // ── Activity Log ───────────────────────────────────────────────────────────

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->dontSubmitEmptyLogs();
    }
}
