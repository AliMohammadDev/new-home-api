<?php

namespace Database\Seeders;

use App\Models\Category;
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
        'name' => ['en' => 'Cookware', 'ar' => 'أواني الطبخ'],
        'description' => [
          'en' => 'Discover the art of cooking with our premium cookware collection. Thoughtfully crafted for durability, performance, and style, each piece ensures even heat distribution and precise results every time. Designed for modern kitchens, our cookware combines high-quality materials with ergonomic design — helping you create meals that look as good as they taste.',
          'ar' => 'اكتشف فن الطبخ مع مجموعتنا المميزة من أدوات الطبخ. مصممة بعناية للتحمل والأداء والأناقة، تضمن كل قطعة توزيع حرارة متساوي ونتائج دقيقة في كل مرة. صممت لمطابخ حديثة، تجمع أدواتنا بين مواد عالية الجودة وتصميم مريح.'
        ],
        'image' => 'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765782469/Cookware_otjpq6.png',
      ],
      [
        'name' => ['en' => 'Tableware', 'ar' => 'أطقم المائدة'],
        'description' => [
          'en' => 'Elevate every dining experience with our beautifully crafted tableware collection. Designed for both everyday meals and special occasions, each piece combines timeless style with lasting quality. Made from premium materials and refined finishes, our tableware enhances any table setting — adding elegance, balance, and charm to your dining moments.',
          'ar' => 'ارتقِ بكل تجربة طعام مع مجموعتنا المصممة من أدوات المائدة الجميلة. مناسبة للوجبات اليومية والمناسبات الخاصة، تجمع كل قطعة بين الأناقة الكلاسيكية والجودة الدائمة.'
        ],
        'image' => 'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765782432/Tableware_gxzzx8.png',
      ],
      [
        'name' => ['en' => 'Kitchen Tools', 'ar' => 'مستلزمات المطبخ'],
        'description' => [
          'en' => 'Essential kitchenware for cooking, prep, and serving—durable, practical, and designed for everyday use.',
          'ar' => 'أدوات مطبخ أساسية للطهي والتحضير والتقديم — متينة وعملية ومصممة للاستخدام اليومي.'
        ],
        'image' => 'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765782445/Kitchenware_ejtsmk.png',
      ],
      [
        'name' => ['en' => 'Bakeware', 'ar' => 'أدوات الخبيز'],
        'description' => [
          'en' => 'Durable and versatile bakeware designed for even baking and easy release. Perfect for cakes, cookies, muffins, and more.',
          'ar' => 'أدوات خبز متينة ومتعددة الاستخدامات مصممة للخبز المتساوي وسهولة الإزالة. مثالية للكعك والبسكويت والمفن والمزيد.'
        ],
        'image' => 'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765697897/bakeWare_lhwmob.png',
      ],
      [
        'name' => ['en' => 'Kitchen Appliances', 'ar' => 'أجهزة المطبخ'],
        'description' => [
          'en' => 'Durable and versatile appliances for your kitchen, ensuring convenience, efficiency, and reliability for all your cooking needs.',
          'ar' => 'أجهزة متينة ومتعددة الاستخدامات لمطبخك، تضمن الراحة والكفاءة والموثوقية لجميع احتياجاتك في الطهي.'
        ],
        'image' => 'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765711498/Aoppliances_hrauey.png',
      ],
      [
        'name' => ['en' => 'Drinkware', 'ar' => 'أأدوات الضيافة'],
        'description' => [
          'en' => "Discover the perfect balance of performance, durability, and design with our premium cookware collection. Each piece is crafted to deliver even heat distribution, reliable cooking results, and long-lasting quality. From everyday meals to gourmet creations, our cookware helps you cook with confidence and style. Designed for comfort, efficiency, and versatility, it’s the ideal companion for every modern kitchen.",
          'ar' => 'اكتشف التوازن المثالي بين الأداء والمتانة والتصميم مع مجموعتنا المميزة من أدوات الشرب. كل قطعة مصممة لتوفير توزيع حرارة متساوي، ونتائج طهي موثوقة، وجودة طويلة الأمد. من الوجبات اليومية إلى الإبداعات الفاخرة، تساعدك أدواتنا على الطهي بثقة وأناقة.'
        ],
        'image' => 'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765782458/Drinkware_kdbeql.png',
      ],
      [
        'name' => ['en' => 'Home Essentials', 'ar' => 'ديكور المنزل'],
        'description' => [
          'en' => 'Essential home tools and accessories crafted to enhance comfort, style, and functionality in your living space.',
          'ar' => 'أدوات وإكسسوارات منزلية أساسية مصممة لتعزيز الراحة والأناقة والوظائف في مساحتك المعيشية.'
        ],
        'image' => 'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765697894/forHome_vnwztz.png',
      ],
    ];

    foreach ($categories as $categoryData) {
      $category = Category::updateOrCreate(
        ['name->en' => $categoryData['name']['en']],
        [
          'name' => $categoryData['name'],
          'description' => $categoryData['description'],
        ]
      );
    }
  }
}