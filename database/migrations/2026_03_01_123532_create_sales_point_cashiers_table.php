<?php

use App\Models\SalesPoint;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('sales_point_cashiers', function (Blueprint $table) {
      $table->id();
      $table->foreignIdFor(SalesPoint::class);
      $table->foreignIdFor(User::class)->constrained();
      $table->string('shift_type')->nullable();
      $table->decimal('daily_limit', 12, 2)->default(0);

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('sales_point_cashiers');
  }
};