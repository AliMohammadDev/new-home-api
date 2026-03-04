<?php

use App\Models\SalesPoint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('company_sales_transfers', function (Blueprint $table) {
      $table->id();

      $table->foreignIdFor(SalesPoint::class)->constrained();
      $table->enum('trans_type', ['deposit', 'withdraw']);
      $table->string('name')->nullable();
      $table->date('date');
      $table->decimal('quantity');
      $table->text('note')->nullable();


      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('company_sales_transfers');
  }
};