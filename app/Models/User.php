<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_WAITER = 'waiter';

    public const DEFAULT_WAITER_PASSWORD = '1234abcd';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'title',
        'role',
        'avatar_url',
        'avatar_public_id',
        'password',
        'must_change_password',
        'password_change_code',
        'password_change_code_expires_at',
    ];

    public function getInitialsAttribute(): string
    {
        $parts = preg_split('/\s+/', trim($this->name)) ?: [];

        return strtoupper(collect($parts)->take(2)->map(fn (string $part): string => $part[0] ?? '')->implode(''));
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isWaiter(): bool
    {
        return $this->role === self::ROLE_WAITER;
    }

    public function issuePasswordChangeCode(string $plainCode): void
    {
        $this->forceFill([
            'password_change_code' => Hash::make($plainCode),
            'password_change_code_expires_at' => now()->addDay(),
        ])->save();
    }

    public function clearPasswordChangeCode(): void
    {
        $this->forceFill([
            'password_change_code' => null,
            'password_change_code_expires_at' => null,
        ])->save();
    }

    public function passwordChangeCodeIsValid(?string $plainCode): bool
    {
        if ($plainCode === null || $plainCode === '' || blank($this->password_change_code)) {
            return false;
        }

        if ($this->password_change_code_expires_at && $this->password_change_code_expires_at->isPast()) {
            return false;
        }

        return Hash::check($plainCode, $this->password_change_code);
    }

    /**
     * @return HasOne<Worker, $this>
     */
    public function worker(): HasOne
    {
        return $this->hasOne(Worker::class);
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * @return array<string, mixed>
     */
    public function getJWTCustomClaims(): array
    {
        return [
            'role' => $this->role,
        ];
    }

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'password_change_code',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
            'password_change_code_expires_at' => 'datetime',
        ];
    }
}
