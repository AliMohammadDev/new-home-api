<?php

use App\Models\CompanyTreasure;
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
    Schema::create('company_entries', function (Blueprint $table) {
      $table->id();
      $table->foreignIdFor(CompanyTreasure::class)->constrained();
      $table->foreignIdFor(User::class)->constrained();
      $table->enum('trans_type', ['deposit', 'withdraw']);
      $table->string('name');
      $table->double('amount');
      $table->softDeletes();
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