<?php

use App\Models\Category;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('products', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->text('body');
      $table->foreignIdFor(Category::class);
      $table->string('image');
      $table->string('image_public_id')->nullable(); //Cloudinary
      $table->decimal('price', 10, 2);
      $table->decimal('discount', 10, 2)->default(0);
      $table->boolean('is_featured')->default(false);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('products');
  }
};
