<?php

use App\Models\Color;
use App\Models\Material;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('product_variants', function (Blueprint $table) {
      $table->id();
      $table->foreignIdFor(Product::class)->constrained()->cascadeOnDelete();
      $table->foreignIdFor(Color::class)->constrained();
      $table->foreignIdFor(Size::class)->constrained();
      $table->foreignIdFor(Material::class)->constrained();
      $table->integer('stock_quantity')->default(0);
      $table->string('sku')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('product_variants');
  }
};
