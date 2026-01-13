<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {

    return [
      'name' => [
        'en' => ucfirst($this->faker->words(2, true)),
        'ar' => ucfirst($this->faker->words(2, true)),
      ],
      'body' => [
        'en' => $this->faker->paragraph(),
        'ar' => $this->faker->paragraph(),
      ],
      'category_id' => Category::inRandomOrder()->first()->id ?? Category::factory(),
      'is_featured' => $this->faker->boolean(20),
    ];
  }

  // public function configure()
  // {
  //   return $this->afterCreating(function (Product $product) {
  //     $images = [
  //       'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765362028/product26_dtvezt.png',
  //       'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765362027/product25_znewrk.png',
  //       'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765362027/product24_wtqy1b.png',
  //       'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765362012/product23_dyuq2s.png',
  //       'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765362011/product22_ozcq5j.png',
  //       'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765362011/product21_scks0d.png',
  //       'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765361097/product13_zm7evn.png',
  //       'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765361097/product12_cyevgx.png',
  //       'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765361097/product11_iziqjk.png',
  //       'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765360179/product3_wqtz8x.png',
  //       'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765360178/product4_gffzpk.png',
  //       'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765360178/product5_dtuw99.png',
  //       'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765360175/product2_c9f42c.png',
  //       'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765360173/product1_tb5wqp.png',
  //       'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765358899/forHome_qzgnuf.png',
  //       'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765358899/cookWare_jr8zzy.png',
  //       'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765358898/drinkWare_bd59t8.png',
  //       'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765358898/tableWare_v5v14i.png',
  //       'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765358898/kitchenWare_vy9qnp.png',
  //       'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765358898/bakeWare_kbtsga.png',
  //       'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765711324/Aoppliances_vlcdaz.png',
  //     ];

  //     $imageUrl = $this->faker->randomElement($images);

  //     $product->addMediaFromUrl($imageUrl)
  //       ->toMediaCollection('product_images');
  //   });
  // }
}
