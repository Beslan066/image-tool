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
        // Проверяем, что premium_until - объект Carbon и будущая дата
        if (!$this->is_premium) {
            return false;
        }

        if ($this->premium_until === null) {
            return true;
        }

        // Теперь premium_until будет Carbon объектом благодаря casts
        return $this->premium_until->isFuture();
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
