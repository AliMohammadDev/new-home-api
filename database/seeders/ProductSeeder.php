<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
  public function run(): void
  {
    $categories = Category::all()
      ->mapWithKeys(fn($cat) => [$cat->name['en'] => $cat->id]);

    $products = [

      // ================= Cookware =================
      [
        'name' => ['en' => 'Non-Stick Frying Pan', 'ar' => 'مقلاة غير لاصقة'],
        'body' => [
          'en' => 'High-quality non-stick frying pan for everyday cooking.',
          'ar' => 'مقلاة غير لاصقة عالية الجودة للطهي اليومي.'
        ],
        'category' => 'Cookware',
        'is_featured' => true,
      ],
      [
        'name' => ['en' => 'Stainless Steel Cooking Pot', 'ar' => 'قدر ستانلس ستيل'],
        'body' => [
          'en' => 'Durable stainless steel pot suitable for soups and stews.',
          'ar' => 'قدر ستانلس ستيل متين للشوربات واليخنات.'
        ],
        'category' => 'Cookware',
        'is_featured' => false,
      ],
      [
        'name' => ['en' => 'Cast Iron Skillet', 'ar' => 'مقلاة حديد زهر'],
        'body' => [
          'en' => 'Heavy-duty cast iron skillet with even heat distribution.',
          'ar' => 'مقلاة حديد زهر بتوزيع حرارة متساوي.'
        ],
        'category' => 'Cookware',
        'is_featured' => false,
      ],

      // ================= Tableware =================
      [
        'name' => ['en' => 'Ceramic Dinner Plates Set', 'ar' => 'طقم صحون سيراميك'],
        'body' => [
          'en' => 'Elegant ceramic plate set for daily dining.',
          'ar' => 'طقم صحون سيراميك أنيق للاستخدام اليومي.'
        ],
        'category' => 'Tableware',
        'is_featured' => true,
      ],
      [
        'name' => ['en' => 'Modern Cutlery Set', 'ar' => 'طقم أدوات مائدة عصري'],
        'body' => [
          'en' => 'Minimal stainless steel cutlery set.',
          'ar' => 'طقم أدوات مائدة من الستانلس ستيل.'
        ],
        'category' => 'Tableware',
        'is_featured' => false,
      ],
      [
        'name' => ['en' => 'Serving Bowl Set', 'ar' => 'طقم أوعية تقديم'],
        'body' => [
          'en' => 'Stylish serving bowls for salads and sides.',
          'ar' => 'أوعية تقديم أنيقة للسلطات والمقبلات.'
        ],
        'category' => 'Tableware',
        'is_featured' => false,
      ],

      // ================= Kitchenware =================
      [
        'name' => ['en' => 'Silicone Cooking Utensils', 'ar' => 'أدوات طبخ سيليكون'],
        'body' => [
          'en' => 'Heat-resistant silicone utensils.',
          'ar' => 'أدوات طبخ سيليكون مقاومة للحرارة.'
        ],
        'category' => 'Kitchenware',
        'is_featured' => true,
      ],
      [
        'name' => ['en' => 'Wooden Cutting Board', 'ar' => 'لوح تقطيع خشبي'],
        'body' => [
          'en' => 'Natural wooden cutting board.',
          'ar' => 'لوح تقطيع خشبي طبيعي.'
        ],
        'category' => 'Kitchenware',
        'is_featured' => false,
      ],
      [
        'name' => ['en' => 'Kitchen Storage Jars', 'ar' => 'برطمانات تخزين'],
        'body' => [
          'en' => 'Glass jars for kitchen storage.',
          'ar' => 'برطمانات زجاجية لتخزين المطبخ.'
        ],
        'category' => 'Kitchenware',
        'is_featured' => false,
      ],

      // ================= Bakeware =================
      [
        'name' => ['en' => 'Non-Stick Baking Tray', 'ar' => 'صينية خبز غير لاصقة'],
        'body' => [
          'en' => 'Perfect for cookies and pastries.',
          'ar' => 'مثالية للبسكويت والمعجنات.'
        ],
        'category' => 'Bakeware',
        'is_featured' => true,
      ],
      [
        'name' => ['en' => 'Cake Baking Mold', 'ar' => 'قالب كيك'],
        'body' => [
          'en' => 'Even baking cake mold.',
          'ar' => 'قالب كيك للخبز المتساوي.'
        ],
        'category' => 'Bakeware',
        'is_featured' => false,
      ],
      [
        'name' => ['en' => 'Muffin Baking Pan', 'ar' => 'صينية مافن'],
        'body' => [
          'en' => 'Ideal muffin and cupcake pan.',
          'ar' => 'صينية مثالية للمافن والكب كيك.'
        ],
        'category' => 'Bakeware',
        'is_featured' => false,
      ],

      // ================= Appliances =================
      [
        'name' => ['en' => 'Electric Kettle', 'ar' => 'غلاية كهربائية'],
        'body' => [
          'en' => 'Fast boiling electric kettle.',
          'ar' => 'غلاية كهربائية سريعة.'
        ],
        'category' => 'Appliances',
        'is_featured' => true,
      ],
      [
        'name' => ['en' => 'Hand Blender', 'ar' => 'خلاط يدوي'],
        'body' => [
          'en' => 'Powerful hand blender.',
          'ar' => 'خلاط يدوي قوي.'
        ],
        'category' => 'Appliances',
        'is_featured' => false,
      ],
      [
        'name' => ['en' => 'Toaster Machine', 'ar' => 'محمصة خبز'],
        'body' => [
          'en' => 'Compact toaster for daily use.',
          'ar' => 'محمصة خبز مدمجة.'
        ],
        'category' => 'Appliances',
        'is_featured' => false,
      ],

      // ================= Drinkware =================
      [
        'name' => ['en' => 'Glass Water Set', 'ar' => 'طقم كاسات زجاج'],
        'body' => [
          'en' => 'Elegant glass set.',
          'ar' => 'طقم زجاج أنيق.'
        ],
        'category' => 'Drinkware',
        'is_featured' => true,
      ],
      [
        'name' => ['en' => 'Thermal Coffee Mug', 'ar' => 'كوب قهوة حراري'],
        'body' => [
          'en' => 'Keeps drinks hot or cold.',
          'ar' => 'يحافظ على حرارة المشروبات.'
        ],
        'category' => 'Drinkware',
        'is_featured' => false,
      ],
      [
        'name' => ['en' => 'Tea Cup Set', 'ar' => 'طقم أكواب شاي'],
        'body' => [
          'en' => 'Classic tea cup set.',
          'ar' => 'طقم أكواب شاي كلاسيكي.'
        ],
        'category' => 'Drinkware',
        'is_featured' => false,
      ],

      // ================= For Home =================
      [
        'name' => ['en' => 'Modern Storage Basket', 'ar' => 'سلة تخزين'],
        'body' => [
          'en' => 'Organize your home.',
          'ar' => 'تنظيم أنيق للمنزل.'
        ],
        'category' => 'For Home',
        'is_featured' => true,
      ],
      [
        'name' => ['en' => 'Decorative Table Tray', 'ar' => 'صينية ديكور'],
        'body' => [
          'en' => 'Decorative serving tray.',
          'ar' => 'صينية تقديم ديكورية.'
        ],
        'category' => 'For Home',
        'is_featured' => false,
      ],
      [
        'name' => ['en' => 'Scented Candle', 'ar' => 'شمعة معطرة'],
        'body' => [
          'en' => 'Relaxing scented candle.',
          'ar' => 'شمعة معطرة للاسترخاء.'
        ],
        'category' => 'For Home',
        'is_featured' => true,
      ],
    ];


    foreach ($products as $product) {
      Product::updateOrCreate(
        ['name->en' => $product['name']['en']],
        [
          'name' => $product['name'],
          'body' => $product['body'],
          'category_id' => $categories[$product['category']] ?? null,
          'is_featured' => $product['is_featured'],
        ]
      );
    }
  }
}