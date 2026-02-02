<?php

namespace MrFelipeMartins\Helix\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use MrFelipeMartins\Helix\Models\VectorActivity;

class PruneActivitiesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'helix:prune
        {--days=30 : Delete activities older than this many days}
        {--before= : Delete activities created before a specific date}
        {--dry-run : Show how many rows would be deleted without deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune old Helix activity records.';

    public function handle(): int
    {
        $cutoff = $this->resolveCutoff();
        if (! $cutoff) {
            return self::FAILURE;
        }

        $query = VectorActivity::query()
            ->where('created_at', '<', $cutoff);

        $count = $query->count();

        if ($this->option('dry-run')) {
            $this->info("{$count} activity records would be deleted (before {$cutoff->toDateTimeString()}).");

            return self::SUCCESS;
        }

        $deleted = $query->delete();

        $this->info("Deleted {$deleted} activity records (before {$cutoff->toDateTimeString()}).");

        return self::SUCCESS;
    }

    protected function resolveCutoff(): ?Carbon
    {
        $before = $this->option('before');

        if (is_string($before) && $before !== '') {
            try {
                return Carbon::parse($before);
            } catch (\Throwable $e) {
                $this->error('Invalid --before date. Use a valid date string, e.g. 2024-01-31.');

                return null;
            }
        }

        $days = (int) $this->option('days');
        if ($days < 0) {
            $this->error('--days must be a positive integer.');

            return null;
        }

        return now()->subDays($days);
    }
}
