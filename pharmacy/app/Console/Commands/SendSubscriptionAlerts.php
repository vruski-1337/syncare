<?php

namespace App\Console\Commands;

use App\Mail\SubscriptionExpiring;
use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

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
    protected $description = 'Send reminder emails for subscriptions expiring within 7 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiring = Subscription::with('company.users')
            ->whereDate('end_date', '<=', now()->addDays(7))
            ->where('active', true)
            ->whereNull('reminder_sent_at')
            ->get();

        $sent = 0;

        foreach ($expiring as $sub) {
            $company = $sub->company;
            if (! $company) {
                continue;
            }

            $owner = $company->users()->where('role', 'owner')->first();

            if ($owner && $owner->email) {
                Mail::to($owner->email)->send(new SubscriptionExpiring($sub));
                $sub->reminder_sent_at = now();
                $sub->save();
                $sent++;
            }
        }

        $this->info("Subscription alerts sent: {$sent}");

        return self::SUCCESS;
    }
}
