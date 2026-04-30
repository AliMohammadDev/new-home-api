<?php

namespace App\Jobs;

use App\Models\PersonalWithdrawal;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ArchiveDailyPersonalWithdrawalEntries implements ShouldQueue
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
    PersonalWithdrawal::query()->delete();
  }
}
