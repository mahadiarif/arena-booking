<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'label', 'type', 'options', 'is_required',
        'is_active', 'sort_order', 'placeholder',
    ];

    protected $casts = [
        'options'     => 'array',
        'is_required' => 'boolean',
        'is_active'   => 'boolean',
        'sort_order'  => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function values(): HasMany
    {
        return $this->hasMany(BookingAttributeValue::class, 'attribute_id');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
