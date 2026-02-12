<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductImport>
 */
class ProductImportFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $suppliers = [
      'شركة المجد للتجارة والاستيراد',
      'مجموعة الفارس اللوجستية',
      'مؤسسة النور للتوريدات العمومية',
      'شركة الشام الدولية للاستيراد',
      'مكتب الهلال للتجارة الخارجية',
      'شركة الراية للخدمات اللوجستية',
      'مؤسسة دبي العالمية للمنسوجات',
      'شركة الأمانة للمفروشات والمنزل'
    ];

    $addresses = [
      'سوريا، دمشق، المنطقة الحرة',
      'الإمارات، دبي، ميناء جبل علي',
      'مصر، القاهرة، مدينة نصر',
      'السعودية، الرياض، حي الملز',
      'لبنان، بيروت، كورنيش المزرعة',
      'الأردن، عمان، شارع مكة'
    ];

    return [
      'supplier_name' => $this->faker->randomElement($suppliers),
      'address' => $this->faker->randomElement($addresses),
      'import_date' => $this->faker->date(),
      'quantity' => $this->faker->numberBetween(100, 500),
      'notes' => $this->faker->randomElement([
        'شحنة ممتازة الجودة',
        'تم الفحص والاستلام',
        'تحتاج إلى تغليف إضافي',
        'شحنة عاجلة لموسم الأعياد',
        null
      ]),
    ];
  }
}
