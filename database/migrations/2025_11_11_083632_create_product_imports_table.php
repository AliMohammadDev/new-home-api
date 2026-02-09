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
    Schema::create('product_imports', function (Blueprint $table) {
      $table->id();

      $table->integer('quantity')->nullable();
      $table->string('supplier_name');
      $table->string('address');
      $table->date('import_date');
      $table->text('notes')->nullable();

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('product_imports');
  }
};
