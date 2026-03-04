<?php

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
    Schema::create('sales_points', function (Blueprint $table) {
      $table->id();
      $table->foreignIdFor(Warehouse::class)->constrained()->cascadeOnDelete();
      $table->string('name');
      $table->string('location')->nullable();
      $table->string('phone')->nullable();
      $table->decimal('amount', 8, 2)->default(0);
      $table->boolean('is_active')->default(true);

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('sales_points');
  }
};