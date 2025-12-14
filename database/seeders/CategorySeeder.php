<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $categories = [
      [
        'name' => 'Cookware',
        'description' => 'Premium cookware for modern kitchens',
        'image' => 'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765697897/cookWare_ebauy6.png',
      ],
      [
        'name' => 'Tableware',
        'description' => 'Elegant tableware for everyday dining',
        'image' => 'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765697894/tableWare_h0xs5q.png',
      ],
      [
        'name' => 'Kitchenware',
        'description' => 'Essential kitchen tools',
        'image' => 'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765697896/kitchenWare_ptecqi.png',
      ],
      [
        'name' => 'Bakeware',
        'description' => 'High quality bakeware',
        'image' => 'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765697897/bakeWare_lhwmob.png',
      ],
      [
        'name' => 'Aoppliances',
        'description' => 'Smart home appliances',
        'image' => 'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765697897/cookWare_ebauy6.png',
      ],
      [
        'name' => 'Drinkware',
        'description' => 'Stylish drinkware collection',
        'image' => 'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765697899/drinkWare_wz7t0x.png',
      ],
      [
        'name' => 'Forhome',
        'description' => 'Home essentials',
        'image' => 'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765697894/forHome_vnwztz.png',
      ],
    ];

    foreach ($categories as $category) {
      $imagePublicId = pathinfo($category['image'], PATHINFO_FILENAME);

      Category::updateOrCreate(
        ['name' => $category['name']],
        [
          'description' => $category['description'],
          'image' => $category['image'],
          'image_public_id' => $imagePublicId,
        ]
      );
    }
  }
}