<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Auction;
use App\Services\AuctionService;

class CheckEndedAuctions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auctions:check-ended';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically start upcoming auctions and end expired ones.';

    /**
     * Execute the console command.
     */
    public function handle(AuctionService $auctionService)
    {
        $this->info('[' . now()->toDateTimeString() . '] Starting auction scheduler check...');

        // 1. Auto-Start: Scheduled auctions whose start time has arrived
        $toStart = Auction::where('status', 'scheduled')
            ->where('start_time', '<=', now())
            ->get();

        if ($toStart->isNotEmpty()) {
            $this->info("Found {$toStart->count()} auctions to start.");
            foreach ($toStart as $auction) {
                $auction->update(['status' => 'live']);
                $this->line("-> Started auction ID: {$auction->id} ({$auction->title})");
            }
        } else {
            $this->line("No upcoming auctions to start.");
        }

        // 2. Auto-End: Live auctions whose end time has passed
        $toEnd = Auction::where('status', 'live')
            ->where('end_time', '<=', now())
            ->get();

        if ($toEnd->isNotEmpty()) {
            $this->info("Found {$toEnd->count()} auctions to end.");
            foreach ($toEnd as $auction) {
                $this->line("-> Ending auction ID: {$auction->id} ({$auction->title})...");
                try {
                    $auctionService->endAuction($auction);
                    $this->info("--> Successfully processed auction ID: {$auction->id}");
                } catch (\Exception $e) {
                    $this->error("--> Error processing auction ID {$auction->id}: " . $e->getMessage());
                }
            }
        } else {
            $this->line("No live auctions to end.");
        }

        $this->info('Auction scheduler check completed.');
    }
}
