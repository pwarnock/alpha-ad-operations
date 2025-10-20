<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'email',
        'role',
        'token',
        'expires_at',
        'accepted_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    public function scopePending($query)
    {
        return $query->whereNull('accepted_at')
                   ->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now())
                   ->whereNull('accepted_at');
    }

    // SaaSykit compatibility
    public function isValid(): bool
    {
        return is_null($this->accepted_at) && 
               $this->expires_at->isFuture();
    }

    public function accept(User $user): void
    {
        $this->update([
            'accepted_at' => now(),
        ]);

        // Add user to tenant
        $this->tenant->users()->attach($user->id, [
            'role' => $this->role,
            'joined_at' => now(),
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invitation) {
            if (empty($invitation->token)) {
                $invitation->token = Str::random(32);
            }
        });
    }
}