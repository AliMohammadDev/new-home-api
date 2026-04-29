<?php

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
    Schema::create('expenses', function (Blueprint $table) {
      $table->id();
      $table->foreignIdFor(User::class)->constrained();
      $table->string('reason');
      $table->decimal('amount', 15, 2);
      $table->dateTime('expense_date');
      $table->softDeletes();
      $table->timestamps();
    });

    Schema::create('personal_withdrawals', function (Blueprint $table) {
      $table->id();
      $table->string('user_name');
      $table->string('reason');
      $table->decimal('amount', 15, 2);
      $table->dateTime('expense_date');
      $table->softDeletes();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('expenses');
    Schema::dropIfExists('personal_withdrawals');
  }
};
