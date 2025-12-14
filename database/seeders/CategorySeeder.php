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
        'description' => 'Discover the art of cooking with our premium cookware collection.
            Thoughtfully crafted for durability, performance, and style, each
            piece ensures even heat distribution and precise results every time.
            Designed for modern kitchens, our cookware combines high-quality
            materials with ergonomic design — helping you create meals that look
            as good as they taste.',
        'image' => 'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765697897/cookWare_ebauy6.png',
      ],
      [
        'name' => 'Tableware',
        'description' => 'Elevate every dining experience with our beautifully crafted
            tableware collection. Designed for both everyday meals and special
            occasions, each piece combines timeless style with lasting quality.
            Made from premium materials and refined finishes, our tableware
            enhances any table setting — adding elegance, balance, and charm to
            your dining moments.',
        'image' => 'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765697894/tableWare_h0xs5q.png',
      ],
      [
        'name' => 'Kitchenware',
        'description' => 'Essential kitchenware for cooking, prep, and serving—durable,
            practical, and designed for everyday use.',
        'image' => 'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765697896/kitchenWare_ptecqi.png',
      ],
      [
        'name' => 'Bakeware',
        'description' => 'Discover the art of cooking with our premium cookware collection.
            Thoughtfully crafted for durability, performance, and style, each
            piece ensures even heat distribution and precise results every time.
            Designed for modern kitchens, our cookware combines high-quality
            materials with ergonomic design — helping you create meals that look
            as good as they taste.',
        'image' => 'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765697897/bakeWare_lhwmob.png',
      ],
      [
        'name' => 'Aoppliances',
        'description' => 'Durable and versatile bakeware designed for even baking and easy
            release. Perfect for cakes, cookies, muffins, and more.',
        'image' => 'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765697897/cookWare_ebauy6.png',
      ],
      [
        'name' => 'Drinkware',
        'description' => "Discover the perfect balance of performance, durability, and design with our premium cookware collection. Each piece is crafted to deliver even heat distribution, reliable cooking results, and long-lasting quality. From everyday meals to gourmet creations, our cookware helps you cook with confidence and style. Designed for comfort, efficiency, and versatility, it’s the ideal companion for every modern kitchen.",

        'image' => 'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765697899/drinkWare_wz7t0x.png',
      ],
      [
        'name' => 'Forhome',
        'description' => 'Discover the art of cooking with our premium cookware collection.
            Thoughtfully crafted for durability, performance, and style, each
            piece ensures even heat distribution and precise results every time.
            Designed for modern kitchens, our cookware combines high-quality
            materials with ergonomic design — helping you create meals that look
            as good as they taste.',
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
