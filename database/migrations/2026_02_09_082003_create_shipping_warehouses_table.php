<?php

use App\Models\ProductVariant;
use App\Models\Warehouse;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('shipping_warehouses', function (Blueprint $table) {
      $table->id();
      $table->foreignIdFor(ProductVariant::class)->constrained();
      $table->foreignIdFor(Warehouse::class)->constrained();
      $table->string('arrival_time');
      $table->integer('amount');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('shipping_warehouses');
  }
};
