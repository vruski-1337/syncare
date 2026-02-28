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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $soon = now()->addDays(7);
        $subs = \App\Models\Subscription::with('company')
            ->where('end_date','<=',$soon)
            ->where('active',true)
            ->whereNull('reminder_sent_at')
            ->get();

        foreach ($subs as $s) {
            // TODO: send actual email using configured mailer
            \Log::info("Subscription for company {$s->company->name} expiring on {$s->end_date}");
            $s->reminder_sent_at = now();
            $s->save();
        }
    }
}
