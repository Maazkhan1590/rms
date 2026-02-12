<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Grant;
use App\Models\ApprovalWorkflow;

class FixGrantStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grants:fix-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix grant_status for grants with completed workflows';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Fixing grant statuses for grants with completed workflows...');
        $this->newLine();

        // Find all grants with workflows that are approved but grant_status is not approved
        $grants = Grant::whereHas('workflow', function($query) {
            $query->where('status', 'approved');
        })
        ->where('grant_status', '!=', 'approved')
        ->get();

        $count = 0;
        $bar = $this->output->createProgressBar($grants->count());
        $bar->start();

        foreach ($grants as $grant) {
            $workflow = $grant->workflow;
            
            if ($workflow && $workflow->status === 'approved') {
                $grant->grant_status = 'approved';
                $grant->status = 'approved';
                $grant->save();
                $count++;
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Fixed {$count} grant(s) with completed workflows.");
        
        return Command::SUCCESS;
    }
}
