<?php

namespace App\Console\Commands;

use App\Services\WaitlistService;
use Illuminate\Console\Command;

class ExpireWaitlistEntries extends Command
{
    protected $signature   = 'waitlist:expire-entries';
    protected $description = 'Expire waitlist holds that were not acted upon within the hold window';

    public function handle(): int
    {
        $count = app(WaitlistService::class)->expireEntries();

        $this->info("✓ {$count} expired waitlist entr" . ($count === 1 ? 'y' : 'ies') . ' processed.');

        return self::SUCCESS;
    }
}
