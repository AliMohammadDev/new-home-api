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
        'images' => [
          'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1770884149/cat2_rwcgp9.jpg',
          'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1770884145/cat12_ayugyf.jpg',
          'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1770884146/cat7_ymjfbd.jpg',
        ],
      ],
      [
        'name' => ['en' => 'Tableware', 'ar' => 'أطقم المائدة'],
        'description' => [
          'en' => 'Elevate every dining experience with our beautifully crafted tableware collection. Designed for both everyday meals and special occasions, each piece combines timeless style with lasting quality. Made from premium materials and refined finishes, our tableware enhances any table setting — adding elegance, balance, and charm to your dining moments.',
          'ar' => 'ارتقِ بكل تجربة طعام مع مجموعتنا المصممة من أدوات المائدة الجميلة. مناسبة للوجبات اليومية والمناسبات الخاصة، تجمع كل قطعة بين الأناقة الكلاسيكية والجودة الدائمة.'
        ],
        'images' => [
          'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1770884148/cat1_nfflsu.jpg',
          'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1770884148/cat3_arrmd1.jpg',
          'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1770884145/cat4_mha4pv.jpg',
        ],
      ],
      [
        'name' => ['en' => 'Kitchen Tools', 'ar' => 'مستلزمات المطبخ'],
        'description' => [
          'en' => 'Essential kitchenware for cooking, prep, and serving—durable, practical, and designed for everyday use.',
          'ar' => 'أدوات مطبخ أساسية للطهي والتحضير والتقديم — متينة وعملية ومصممة للاستخدام اليومي.'
        ],
        'images' => [
          'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1770884145/cat9_rgcb5j.jpg',
          'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1770884144/cat10_phqjaa.jpg',
          'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1770884142/cat11_qauajd.jpg',
        ],
      ],
      [
        'name' => ['en' => 'Bakeware', 'ar' => 'أدوات الخبيز'],
        'description' => [
          'en' => 'Durable and versatile bakeware designed for even baking and easy release. Perfect for cakes, cookies, muffins, and more.',
          'ar' => 'أدوات خبز متينة ومتعددة الاستخدامات مصممة للخبز المتساوي وسهولة الإزالة. مثالية للكعك والبسكويت والمفن والمزيد.'
        ],
        'images' => [
          'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1770884135/cat19_pdowmh.jpg',
          'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1770884138/cat13_t3uqcf.jpg',
          'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1770884135/cat14_hlvmnb.jpg',
        ],
      ],
      [
        'name' => ['en' => 'Kitchen Appliances', 'ar' => 'أجهزة المطبخ'],
        'description' => [
          'en' => 'Durable and versatile appliances for your kitchen, ensuring convenience, efficiency, and reliability for all your cooking needs.',
          'ar' => 'أجهزة متينة ومتعددة الاستخدامات لمطبخك، تضمن الراحة والكفاءة والموثوقية لجميع احتياجاتك في الطهي.'
        ],
        'images' => [
          'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1770884134/cat17_o7fdyb.jpg',
          'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1770884134/cat15_mbz5rp.jpg',
          'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1770884134/cat18_ve3jcl.jpg',
        ],
      ],
      [
        'name' => ['en' => 'Drinkware', 'ar' => 'أأدوات الضيافة'],
        'description' => [
          'en' => "Discover the perfect balance of performance, durability, and design with our premium cookware collection. Each piece is crafted to deliver even heat distribution, reliable cooking results, and long-lasting quality. From everyday meals to gourmet creations, our cookware helps you cook with confidence and style. Designed for comfort, efficiency, and versatility, it’s the ideal companion for every modern kitchen.",
          'ar' => 'اكتشف التوازن المثالي بين الأداء والمتانة والتصميم مع مجموعتنا المميزة من أدوات الشرب. كل قطعة مصممة لتوفير توزيع حرارة متساوي، ونتائج طهي موثوقة، وجودة طويلة الأمد. من الوجبات اليومية إلى الإبداعات الفاخرة، تساعدك أدواتنا على الطهي بثقة وأناقة.'
        ],
        'images' => [
          'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1770884132/cat22_zbtpfu.jpg',
          'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1770884130/cat24_hps2p1.jpg',
          'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1770884133/cat16_etbyer.jpg',
        ],
      ],
      [
        'name' => ['en' => 'Home Essentials', 'ar' => 'ديكور المنزل'],
        'description' => [
          'en' => 'Essential home tools and accessories crafted to enhance comfort, style, and functionality in your living space.',
          'ar' => 'أدوات وإكسسوارات منزلية أساسية مصممة لتعزيز الراحة والأناقة والوظائف في مساحتك المعيشية.'
        ],
        'images' => [
          'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1770884130/cat21_sc0cqk.jpg',
          'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1770449595/pexels-artbovich-6956848_gf8bkl.jpg',
          'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1770449587/pexels-hiba-q-omar-106562569-15221141_nmibpz.jpg',
        ],
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