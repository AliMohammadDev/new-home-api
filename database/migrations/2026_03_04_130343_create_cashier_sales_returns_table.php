<?php

use App\Models\CashierReturnFatora;
use App\Models\ProductVariant;
use App\Models\SalesPointCashier;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('cashier_sales_returns', function (Blueprint $table) {
      $table->id();

      $table->foreignIdFor(CashierReturnFatora::class)
        ->constrained()
        ->cascadeOnDelete();

      $table->foreignIdFor(ProductVariant::class)->constrained();
      $table->foreignIdFor(SalesPointCashier::class)->constrained();

      $table->decimal('quantity', 15, 2);
      $table->decimal('price', 15, 2);
      $table->decimal('full_price', 15, 2);

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('cashier_sales_returns');
  }
};