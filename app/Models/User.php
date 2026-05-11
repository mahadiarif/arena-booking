<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles, LogsActivity;

    public function canAccessPanel(Panel $panel): bool
    {
        // Allow users who have any admin/staff role and are active
        return $this->is_active && ($this->hasAnyRole(['super_admin', 'admin', 'staff']) || $this->role === 'admin');
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password'  => 'hashed',
            'is_active' => 'boolean',
            'role'      => 'string',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function bookedBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'booked_by');
    }

    public function createdCustomers(): HasMany
    {
        return $this->hasMany(Customer::class, 'created_by');
    }

    public function customer(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Customer::class);
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
