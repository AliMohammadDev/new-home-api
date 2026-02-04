<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('shipping_cities', function (Blueprint $table) {
      $table->id();
      $table->json('city_name');
      $table->string('estimated_delivery');
      $table->decimal('shipping_fee', 8, 2)->default(0);
      $table->boolean('is_free_shipping')->default(false);
      $table->boolean('is_active')->default(true);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('shipping_cities');
  }
};