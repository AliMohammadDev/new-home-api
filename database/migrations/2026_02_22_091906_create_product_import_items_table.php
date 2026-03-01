<?php

use App\Models\ProductImport;
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
    Schema::create('product_import_items', function (Blueprint $table) {
      $table->id();
      $table->foreignIdFor(ProductImport::class)->constrained();
      $table->foreignIdFor(ProductVariant::class)->nullable()->constrained()
        ->nullOnDelete();

      $table->integer('quantity');
      $table->decimal('price', 10, 2);
      $table->decimal('shipping_price', 10, 2);
      $table->decimal('discount', 10, 2)->default(0);
      $table->timestamp('expected_arrival')->nullable();

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('product_import_itemse');
  }
};
