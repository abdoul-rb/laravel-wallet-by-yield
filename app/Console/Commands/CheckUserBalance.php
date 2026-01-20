<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Notifications\BalanceLowedNotification;
use Illuminate\Console\Command;
use App\Models\User;

class CheckUserBalance extends Command
{
    protected $signature = 'check:user-balance';

    protected $description = 'Check user balance';

    public function handle()
    {
        $users = User::all();

        foreach ($users as $user) {
            if ($user->wallet && $user->wallet->balance < 10) {
                $user->notify(new BalanceLowedNotification());
            }
        }
    }
}
