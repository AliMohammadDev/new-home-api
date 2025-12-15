<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {

    $categories = [
      'Drinkware' => 'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765782458/Drinkware_kdbeql.png',
      'Cookware' => 'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765782469/Cookware_otjpq6.png',
      'Bakeware' => 'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765697897/bakeWare_lhwmob.png',
      'Aoppliances' => 'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765711324/Aoppliances_vlcdaz.png',
      'Kitchenware' => 'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765782445/Kitchenware_ejtsmk.png',
      'ForHome' => 'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765697894/forHome_vnwztz.png',
      'TableWare' => 'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765782432/Tableware_gxzzx8.png',
    ];
    $name = $this->faker->unique()->randomElement(array_keys($categories));
    $imageUrl = $categories[$name];
    $imagePublicId = pathinfo($imageUrl, PATHINFO_FILENAME);

    return [
      'name' => $name,
      'description' => $this->faker->sentence(),
      'image' => $imageUrl,
      'image_public_id' => $imagePublicId,
    ];
  }
}
