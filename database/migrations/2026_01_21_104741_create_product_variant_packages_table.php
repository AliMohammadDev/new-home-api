<?php

use App\Models\ProductVariant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('product_variant_packages', function (Blueprint $table) {
      $table->id();
      $table->foreignIdFor(ProductVariant::class)->constrained();
      $table->integer('quantity');
      $table->decimal('price', 10, 2);

      $table->unique(['product_variant_id', 'quantity']);

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('product_variant_packages');
  }
};