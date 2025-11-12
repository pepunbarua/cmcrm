<?php

namespace App\Console\Commands;

use App\Models\Lead;
use Illuminate\Console\Command;

class ClearExpiredLeadLocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:clear-expired-locks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear expired lead locks';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = Lead::where('lock_expires_at', '<', now())
            ->whereNotNull('locked_by')
            ->update([
                'locked_by' => null,
                'locked_at' => null,
                'lock_expires_at' => null,
            ]);

        $this->info("Cleared {$count} expired lead locks.");
        
        return Command::SUCCESS;
    }
}
