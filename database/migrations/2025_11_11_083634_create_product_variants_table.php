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
      $table->foreignIdFor(Color::class)->nullable()->constrained();
      $table->foreignIdFor(Size::class)->nullable()->constrained();
      $table->foreignIdFor(Material::class)->nullable()->constrained();
      $table->foreignId('product_import_id')->nullable()->constrained('product_imports')->nullOnDelete();
      $table->decimal('price', 10, 2);
      $table->decimal('discount', 10, 2)->default(0);
      $table->integer('stock_quantity')->default(0);
      $table->string('sku')->unique();
      $table->softDeletes();
      $table->timestamps();

      $table->unique(['product_id', 'color_id', 'size_id', 'material_id'], 'product_variant_unique');
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
