<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ProductVariantPackage;
use App\Models\ProductVariant;
use App\Models\Cart;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('cart_items', function (Blueprint $table) {
      $table->id();
      $table->foreignIdFor(Cart::class)->constrained()->cascadeOnDelete();
      $table->foreignIdFor(ProductVariant::class)->constrained()->cascadeOnDelete();
      $table->foreignIdFor(ProductVariantPackage::class)
        ->nullable()
        ->constrained('product_variant_packages')
        ->nullOnDelete();
      $table->integer('quantity')->default(1);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('cart_items');
  }
};