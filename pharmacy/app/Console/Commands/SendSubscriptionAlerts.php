<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendSubscriptionAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-subscription-alerts';

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
        $expiring = \App\Models\Subscription::where('end_date', '<=', now()->addDays(7))
            ->where('active', true)
            ->get();
        foreach ($expiring as $sub) {
            $company = $sub->company;
            $owner = $company->users()->where('role','owner')->first();
            if ($owner && $owner->email) {
                \Mail::to($owner->email)->send(new \App\Mail\SubscriptionExpiring($company));
            }
        }
        $this->info('Subscription alerts sent.');
    }
}
