<?php

namespace MrFelipeMartins\Helix\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'helix:install';

    protected $description = 'Install Helix dashboard';

    public function handle(): int
    {
        $this->info('Installing Helix...');

        $this->call('vendor:publish', [
            '--tag' => 'helix-config',
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'helix-assets',
        ]);

        $this->info('Helix installed successfully.');
        $this->info('Visit /helix in your browser.');

        return self::SUCCESS;
    }
}
