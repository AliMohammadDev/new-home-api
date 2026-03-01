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
    Schema::create('sales_point_managers', function (Blueprint $table) {
      $table->id();
      $table->foreignIdFor(SalesPoint::class)->constrained();
      $table->foreignIdFor(User::class)->constrained();
      $table->string('phone');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('sales_point_managers');
  }
};
