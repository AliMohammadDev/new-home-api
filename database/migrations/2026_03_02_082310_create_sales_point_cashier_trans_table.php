<?php

use App\Models\SalesPoint;
use App\Models\SalesPointCashier;
use App\Models\SalesPointManager;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('sales_point_cashier_trans', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->foreignIdFor(SalesPoint::class)->constrained();
      $table->foreignIdFor(SalesPointManager::class)->constrained();
      $table->foreignIdFor(SalesPointCashier::class)->constrained();
      $table->enum('trans_type', ['deposit', 'withdraw'])->default('deposit');
      $table->decimal('amount', 10, 2)->default(0);
      $table->date('date');
      $table->softDeletes();
      $table->text('note')->nullable();


      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('sales_point_cashier_trans');
  }
};