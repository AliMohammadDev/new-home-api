<?php

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
    Schema::create('cashier_sales_fatoras', function (Blueprint $table) {
      $table->id();

      $table->foreignIdFor(SalesPointCashier::class)
        ->constrained('sales_point_cashiers')
        ->cascadeOnDelete();

      $table->date('date');
      $table->decimal('full_price', 15, 2);


      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('cashier_sales_fatoras');
  }
};
