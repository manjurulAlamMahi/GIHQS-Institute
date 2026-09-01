<?php

namespace App\Console\Commands;

use App\Models\Purchase;
use App\Models\User;
use Illuminate\Console\Command;

class CheckExpiredMemberships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-expired-memberships';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically demotes users whose membership validity period has expired';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired memberships...');

        // Find all paid/active membership purchases that have expired
        $expiredPurchases = Purchase::where('purchase_type', 'membership')
            ->where('payment_status', 'paid')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->get();

        $count = 0;
        foreach ($expiredPurchases as $purchase) {
            // Update order status to completed/expired
            $purchase->update([
                'order_status' => 'completed',
            ]);
            $count++;
        }

        $this->info("Successfully processed {$count} expired memberships.");
    }
}
