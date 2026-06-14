<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'premium_until' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function isPremiumActive(): bool
    {
        $active = $this->is_premium && ($this->premium_until === null || $this->premium_until->isFuture());

        // Отладка
        \Log::info('isPremiumActive check', [
            'user_id' => $this->id,
            'is_premium' => $this->is_premium,
            'premium_until' => $this->premium_until,
            'active' => $active
        ]);

        return $active;
    }

    public function activatePremium(int $months = 1): void
    {
        $this->is_premium = true;

        if ($this->premium_until && $this->premium_until instanceof \DateTimeInterface && $this->premium_until->isFuture()) {
            $this->premium_until = $this->premium_until->addMonths($months);
        } else {
            $this->premium_until = now()->addMonths($months);
        }

        $this->save();
    }
}
