<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Проверка и деактивация просроченных премиум подписок';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $expiredUsers = User::where('is_premium', true)
            ->where('premium_until', '<', now())
            ->get();

        $count = 0;
        foreach ($expiredUsers as $user) {
            $user->update(['is_premium' => false]);
            $this->info("Пользователь {$user->email} - премиум истек");
            $count++;
        }

        $this->info("Проверено {$count} просроченных подписок");

        return Command::SUCCESS;
    }
}
