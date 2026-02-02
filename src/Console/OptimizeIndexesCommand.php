<?php

namespace MrFelipeMartins\Helix\Console;

use Illuminate\Console\Command;
use MrFelipeMartins\Helix\Helix;
use MrFelipeMartins\Helix\Models\Index;

class OptimizeIndexesCommand extends Command
{
    protected $signature = 'helix:optimize';

    protected $description = 'Optimize all Helix indexes (vacuum/rebuild)';

    public function handle(): int
    {
        $this->info('Optimizing all Helix indexes...');

        $count = 0;
        $service = app(Helix::class);

        Index::query()->chunk(50, function ($chunk) use (&$count, $service) {
            foreach ($chunk as $index) {
                $this->line("Optimizing {$index->name} ({$index->id})");
                $service->optimize($index);
                $count++;
            }
        });

        $this->info("Done. Optimized {$count} index(es).");

        return self::SUCCESS;
    }
}
