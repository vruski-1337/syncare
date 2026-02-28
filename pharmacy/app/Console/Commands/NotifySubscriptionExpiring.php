<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class NotifySubscriptionExpiring extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-subscription-expiring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Alias: send reminders for expiring subscriptions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->warn('Deprecated command signature. Running app:send-subscription-alerts instead.');

        return $this->call('app:send-subscription-alerts');
    }
}
