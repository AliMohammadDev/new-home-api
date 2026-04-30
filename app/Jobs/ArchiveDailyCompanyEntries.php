<?php

namespace App\Jobs;

use App\Models\CompanyEntry;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ArchiveDailyCompanyEntries implements ShouldQueue
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
    $count = CompanyEntry::whereNull('deleted_at')->delete();
  }
}
