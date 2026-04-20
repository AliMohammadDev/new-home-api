<?php

use App\Models\ProductVariant;
use App\Models\User;
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
    Schema::create('warehouse_returns', function (Blueprint $table) {
      $table->id();
      $table->foreignIdFor(ProductVariant::class)->constrained();
      $table->foreignIdFor(Warehouse::class)->constrained();
      $table->foreignIdFor(User::class)->constrained();
      $table->timestamp('arrival_time');
      $table->integer('amount');
      $table->string('unit_name')->nullable();
      $table->integer('unit_capacity')->default(1);
      $table->softDeletes();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('warehouse_returns');
  }
};