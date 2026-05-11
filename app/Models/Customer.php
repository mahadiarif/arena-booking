<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Customer extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'nid',
        'organization',
        'address',
        'notes',
        'credit_balance',
        'total_bookings',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'credit_balance' => 'decimal:2',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function activeBookings(): HasMany
    {
        return $this->hasMany(Booking::class)
                    ->whereNotIn('status', ['cancelled', 'no_show']);
    }

    public function creditTransactions(): HasMany
    {
        return $this->hasMany(CreditTransaction::class);
    }

    public function notificationPreferences(): HasMany
    {
        return $this->hasMany(NotificationPreference::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeSearch($query, string $term): void
    {
        $like = '%' . $term . '%';
        $query->where(function ($q) use ($like) {
            $q->where('name', 'LIKE', $like)
              ->orWhere('phone', 'LIKE', $like)
              ->orWhere('email', 'LIKE', $like);
        });
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    /**
     * Check whether the customer has sufficient credit for a given amount.
     * Implemented as a method because accessors cannot accept parameters.
     */
    public function isCreditSufficient(float $amount): bool
    {
        return (float) $this->credit_balance >= $amount;
    }

    // ── Activity Log ───────────────────────────────────────────────────────────

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->dontSubmitEmptyLogs();
    }
}
