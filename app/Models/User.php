<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'organisation_id',
        'magic_link_token',
        'magic_link_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'magic_link_token',
        'magic_link_expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'magic_link_expires_at' => 'datetime',
    ];

    /**
     * Get the organisation that owns the user.
     */
    public function organisation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function projects(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return str_ends_with($this->email, '@konstruktiv.nl') && $this->hasVerifiedEmail();
    }

    /**
     * Check if the magic link token is valid
     */
    public function isMagicLinkValid(string $token): bool
    {
        return $this->magic_link_token === $token
            && $this->magic_link_expires_at
            && $this->magic_link_expires_at->isFuture();
    }
}
