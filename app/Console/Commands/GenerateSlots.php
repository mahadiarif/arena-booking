<?php

namespace App\Console\Commands;

use App\Models\Venue;
use App\Services\SlotGeneratorService;
use Illuminate\Console\Command;

class GenerateSlots extends Command
{
    protected $signature = 'slots:generate
                            {--days=30 : Number of days ahead to generate slots for}
                            {--venue= : Venue ID to generate for (optional — all active venues if omitted)}
                            {--force : Overwrite existing slots with zero bookings}';

    protected $description = 'Generate time slots for venues based on schedule configuration';

    public function handle(): int
    {
        $days    = (int) $this->option('days');
        $venueId = $this->option('venue');
        $force   = (bool) $this->option('force');

        $this->info("Generating slots for {$days} days" . ($force ? ' (force mode)' : '') . '...');

        $from = now()->startOfDay();
        $to   = now()->addDays($days)->endOfDay();

        if ($venueId) {
            $venue = Venue::with('schedule')->findOrFail($venueId);

            $bar = $this->output->createProgressBar(1);
            $bar->start();

            $result = app(SlotGeneratorService::class)->generateForVenue($venue, $from, $to, $force);
            $result['venues'] = 1;

            $bar->finish();
        } else {
            $venues = Venue::active()->with('schedule')->get();

            $bar    = $this->output->createProgressBar($venues->count());
            $bar->start();

            $result = ['created' => 0, 'skipped' => 0, 'venues' => $venues->count()];

            foreach ($venues as $venue) {
                $partial = app(SlotGeneratorService::class)->generateForVenue($venue, $from, $to, $force);
                $result['created'] += $partial['created'];
                $result['skipped'] += $partial['skipped'];
                $bar->advance();
            }

            $bar->finish();
        }

        $this->newLine(2);
        $this->info("✓ {$result['created']} slots created | {$result['skipped']} skipped | {$result['venues']} venues processed");

        return self::SUCCESS;
    }
}
