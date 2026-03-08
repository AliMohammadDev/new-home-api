<?php

use App\Models\CompanyTreasure;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('company_entries', function (Blueprint $table) {
      $table->id();
      $table->foreignIdFor(CompanyTreasure::class)->constrained();
      $table->foreignId('user_id')->constrained();
      $table->enum('trans_type', ['deposit', 'withdrawal']);
      $table->string('name');
      $table->double('amount');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('company_entries');
  }
};