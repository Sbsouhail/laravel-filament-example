<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\Gender;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class User extends Authenticatable implements FilamentUser, HasName, HasMedia
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use InteractsWithMedia;
    use Notifiable;
    use HasApiTokens;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'date_of_birth',
        'gender',
        'first_name',
        'last_name',
        'phone',
        'email',
        'password',
        'is_admin',
        'invite_limit',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'gender' => Gender::class,
        ];
    }

    /** @return HasMany<Device,$this> */
    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    /** @return HasMany<Redemption,$this> */
    public function redemptions(): HasMany
    {
        return $this->hasMany(Redemption::class);
    }

    public function redemptionsCount(): int
    {
        return $this->redemptions()->count();
    }

    /** @return HasMany<ForgotPasswordOtp,$this> */
    public function forgotPasswordOtps(): HasMany
    {
        return $this->hasMany(ForgotPasswordOtp::class);
    }

    /** @return HasMany<InviteCode,$this> */
    public function inviteCodes()
    {
        return $this->hasMany(InviteCode::class);
    }

    public function inviteCodesCount(): int
    {
        return $this->inviteCodes()->count();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // if ($panel->getId() === 'admin') {
        //     return str_ends_with($this->email, '@yourdomain.com') && $this->hasVerifiedEmail();
        // }

        return $this->is_admin;
    }

    public function getFilamentName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function setPasswordAttribute(string $value): void
    {
        if (Hash::needsRehash($value)) {
            $this->attributes['password'] = bcrypt($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }

    /**
     * Specifies the user's FCM token
     *
     * @return string|string[]
     */
    public function routeNotificationForFcm(): string|array
    {
        /** @var string[] $tokens */
        $tokens = $this->devices->pluck('fcm_token')->toArray();

        return $tokens;
    }

    protected static function booted(): void
    {
        static::updated(function (User $user) {
            if ($user->isDirty('password')) {
                $user->tokens()->delete();
            }
        });
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Fit::Contain, 300, 300);
    }
}
