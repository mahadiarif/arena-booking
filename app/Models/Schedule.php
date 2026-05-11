<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Schedule extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'timezone',
        'start_time',
        'end_time',
        'slot_interval_minutes',
        'allowed_days',
        'allow_concurrent',
        'max_concurrent',
        'availability_start',
        'availability_end',
        'pricing_rules',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'allowed_days'       => 'array',
            'pricing_rules'      => 'array',
            'allow_concurrent'   => 'boolean',
            'is_active'          => 'boolean',
            'availability_start' => 'date',
            'availability_end'   => 'date',
        ];
    }

    /**
     * Get the total price for a slot starting at a specific time.
     */
    public function getPriceForTime(string $startTime, float $basePrice): float
    {
        $extra = 0;
        $rules = $this->pricing_rules ?? [];

        foreach ($rules as $rule) {
            $ruleStart = $rule['start_time'];
            $ruleEnd   = $rule['end_time'];

            if ($startTime >= $ruleStart && $startTime < $ruleEnd) {
                $extra += (float) ($rule['extra_price'] ?? 0);
            }
        }

        return $basePrice + $extra;
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function venues(): HasMany
    {
        return $this->hasMany(Venue::class);
    }

    public function slots(): HasMany
    {
        return $this->hasMany(Slot::class);
    }

    public function blackoutPeriods(): HasMany
    {
        return $this->hasMany(BlackoutPeriod::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeActive($query): void
    {
        $query->where('is_active', true);
    }

    // ── Activity Log ───────────────────────────────────────────────────────────

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->dontSubmitEmptyLogs();
    }
}
