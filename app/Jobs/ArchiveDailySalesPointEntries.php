<?php

namespace App\Jobs;

use App\Models\CompanySalesTransferEntry;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ArchiveDailySalesPointEntries implements ShouldQueue
{
  use Queueable;

  /**
   * Create a new job instance.
   */
  public function __construct()
  {
    //
  }

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    CompanySalesTransferEntry::query()->delete();
  }
}
