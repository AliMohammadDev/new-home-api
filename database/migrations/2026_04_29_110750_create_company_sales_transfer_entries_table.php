<?php

use App\Models\CompanySalesTransfer;
use App\Models\CompanyTreasure;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('company_sales_transfer_entries', function (Blueprint $table) {
      $table->id();

      $table->foreignIdFor(CompanySalesTransfer::class)->constrained();
      $table->foreignIdFor(CompanyTreasure::class)->constrained();
      $table->foreignIdFor(User::class)->constrained();
      $table->decimal('amount', 15, 2);
      $table->string('note')->nullable();

      $table->softDeletes();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('company_sales_transfer_entries');
  }
};
