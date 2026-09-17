<?php

namespace App\Console\Commands;

use App\Models\Item;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ExpireItems extends Command
{
    protected $signature = 'items:expire';
    protected $description = 'Activate items after availability time has passed';

    public function handle()
    {
        // Step 1: Get only candidates (status = 0 + availability fields not null)
        $candidates = Item::where('status', 0)
            ->whereNotNull('availability_hours')
            ->whereNotNull('availability_set_at')
            ->get();

        // Step 2: Filter those that have expired based on availability_hours
        $itemsToActivate = $candidates->filter(function ($item) {
            $availableAt = Carbon::parse($item->availability_set_at)
                ->addSeconds($item->availability_hours * 3600);

            return now()->greaterThanOrEqualTo($availableAt);
        });

        // Step 3: Update items
        foreach ($itemsToActivate as $item) {
            $item->status = 1;
            $item->availability_hours = null;
            $item->availability_set_at = null;
            $item->save();
        }

        // Step 4: Log updated IDs
        $this->info('Items activated: ' . $itemsToActivate->pluck('id')->join(', '));
    }
}
