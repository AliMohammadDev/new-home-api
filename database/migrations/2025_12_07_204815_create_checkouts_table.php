<?php

use App\Models\Cart;
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
    Schema::create('checkouts', function (Blueprint $table) {
      $table->id();
      $table->string('first_name');
      $table->string('last_name');
      $table->string('email');
      $table->string('phone');
      $table->string('country');
      $table->string('city');
      $table->string('street')->nullable();
      $table->string('floor')->nullable();
      $table->string('postal_code')->nullable();
      $table->text('additional_information')->nullable();
      $table->foreignIdFor(Cart::class)->constrained();
      $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
      $table->enum('status', ['pending', 'completed'])->default('pending');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('checkouts');
  }
};