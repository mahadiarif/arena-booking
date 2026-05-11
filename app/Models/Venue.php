<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Venue extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'capacity',
        'color',
        'hourly_rate',
        'min_duration_minutes',
        'max_duration_minutes',
        'requires_approval',
        'description',
        'is_active',
        'sort_order',
        'schedule_id',
    ];

    protected function casts(): array
    {
        return [
            'requires_approval' => 'boolean',
            'is_active'         => 'boolean',
            'hourly_rate'       => 'decimal:2',
        ];
    }

    // ── Boot ───────────────────────────────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $venue) {
            if (empty($venue->slug)) {
                $venue->slug = Str::slug($venue->name);
            }
        });
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function slots(): HasMany
    {
        return $this->hasMany(Slot::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(VenueImage::class);
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(VenueImage::class)->where('is_primary', true);
    }

    public function resourceGroups(): BelongsToMany
    {
        return $this->belongsToMany(ResourceGroup::class, 'resource_group_venue');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(VenueReview::class);
    }

    public function getAverageRatingAttribute(): float
    {
        return (float) $this->reviews()->where('is_published', true)->avg('rating') ?: 0.0;
    }

    public function getReviewCountAttribute(): int
    {
        return $this->reviews()->where('is_published', true)->count();
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeActive($query): void
    {
        $query->where('is_active', true);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'stadium'      => 'Stadium',
            'turf_indoor'  => 'Indoor Turf',
            'turf_outdoor' => 'Outdoor Turf',
            'vip_box'      => 'VIP Box',
            'hall'         => 'Hall',
            default        => ucfirst($this->type),
        };
    }

    // ── Activity Log ───────────────────────────────────────────────────────────

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->dontSubmitEmptyLogs();
    }
}
