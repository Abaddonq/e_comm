<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessQueuedJobs extends Command
{
    protected $signature = 'queue:process {--timeout=60 : Maximum seconds to run}';

    protected $description = 'Process queued jobs manually';

    public function handle(): int
    {
        $this->info('Starting queue processing...');
        $this->info('Press Ctrl+C to stop');

        $timeout = (int) $this->option('timeout');

        $this->call('queue:work', [
            '--stop-when-empty' => true,
            '--timeout' => $timeout,
        ]);

        $this->info('Queue processing completed.');

        return Command::SUCCESS;
    }
}
