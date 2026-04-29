<?php

use App\Models\CompanyTreasure;
use App\Models\Expense;
use App\Models\PersonalWithdrawal;
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
    Schema::create('expense_entries', function (Blueprint $table) {
      $table->id();

      $table->foreignIdFor(Expense::class)->constrained();
      $table->foreignIdFor(CompanyTreasure::class)->constrained();
      $table->foreignIdFor(User::class)->constrained();
      $table->decimal('amount', 15, 2);
      $table->string('note')->nullable();

      $table->softDeletes();
      $table->timestamps();
    });

    Schema::create('personal_withdrawal_entries', function (Blueprint $table) {
      $table->id();

      $table->foreignIdFor(PersonalWithdrawal::class)->constrained();
      $table->foreignIdFor(CompanyTreasure::class)->constrained();
      $table->foreignIdFor(User::class)->constrained();
      $table->decimal('amount', 15, 2);
      $table->string('note')->nullable();

      $table->softDeletes();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('expense_entries');
    Schema::dropIfExists('personal_withdrawal_entries');
  }
};
