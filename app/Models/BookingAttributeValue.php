<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingAttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'booking_attribute_id',
        'value',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(BookingAttribute::class, 'booking_attribute_id');
    }
}
