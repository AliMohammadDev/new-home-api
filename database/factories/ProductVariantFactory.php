<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Models\ProductVariantImage;
use App\Models\ProductVariant;
use Illuminate\Support\Str;
use App\Models\Material;
use App\Models\Product;
use App\Models\Color;
use App\Models\Size;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{

  protected $model = ProductVariant::class;
  private $imageUrls = [
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765362028/product26_dtvezt.png',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765362027/product25_znewrk.png',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765362027/product24_wtqy1b.png',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765362012/product23_dyuq2s.png',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765362011/product22_ozcq5j.png',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765362011/product21_scks0d.png',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765361097/product13_zm7evn.png',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765361097/product12_cyevgx.png',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765361097/product11_iziqjk.png',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765360179/product3_wqtz8x.png',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765360178/product4_gffzpk.png',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765360178/product5_dtuw99.png',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765360175/product2_c9f42c.png',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765360173/product1_tb5wqp.png',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765358899/forHome_qzgnuf.png',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765358899/cookWare_jr8zzy.png',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765358898/drinkWare_bd59t8.png',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765358898/tableWare_v5v14i.png',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765358898/kitchenWare_vy9qnp.png',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765358898/bakeWare_kbtsga.png',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1765711324/Aoppliances_vlcdaz.png',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1769237893/prod5_hd4n3f.png',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1769237891/prod6_wbu8s2.png',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1769237891/prod7_spjpbl.png',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1769237891/prod8_usxyxj.png',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1769237891/prod9_w4plue.png',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1769237890/prod12_rfv19k.png',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1769237890/prod14_gjtfmo.png',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1769237890/prod111_utgcep.png',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1770451377/product_hirp67.jpg',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1770451350/products_mygwll.jpg',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1770451273/productssss_p1smps.jpg',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1770451272/productsss_uvmqgj.jpg',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1770451263/productsssss_ctdyzm.jpg',
    'https://res.cloudinary.com/dzvrf9xe3/image/upload/v1770451349/product2_bdkmhy.jpg'
  ];

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $price = $this->faker->randomFloat(2, 0.2, 100);
    return [
      'product_id' => Product::factory(),
      'color_id' => Color::factory(),
      'size_id' => Size::factory(),
      'material_id' => Material::factory(),
      'price' => $price,
      'discount' => $this->faker->numberBetween(0, 50),
      'stock_quantity' => 0,
      'sku' => ProductVariant::generateUniqueSku(),
      'barcode' => ProductVariant::generateUniqueBarcode(),
    ];
  }

  public function configure()
  {

    // create 4 image for each variant
    return $this->afterCreating(function (ProductVariant $variant) {
      for ($i = 0; $i < 4; $i++) {
        try {
          $remoteUrl = $this->faker->randomElement($this->imageUrls);

          $directory = "product_variants/{$variant->id}";
          if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
          }

          $filename = Str::uuid() . '.webp';
          $path = "{$directory}/{$filename}";

          $img = Image::make($remoteUrl)
            ->resize(1000, 1000, function ($constraint) {
              $constraint->aspectRatio();
              $constraint->upsize();
            })
            ->encode('webp', 70);

          Storage::disk('public')->put($path, (string) $img);

          ProductVariantImage::create([
            'product_variant_id' => $variant->id,
            'image' => $filename,
          ]);

        } catch (\Exception $e) {
          logger()->error("Failed to seed image $i for variant {$variant->id}: " . $e->getMessage());
        }
      }

      // create packages logic
      $quantities = [6, 12, 24];

      foreach ($quantities as $qty) {
        $singleUnitFinalPrice = $variant->final_price;

        $bulkDiscountPercentage = match ($qty) {
          6 => 0.10,
          12 => 0.15,
          24 => 0.20,
          default => 0.05
        };

        $pricePerUnitInPackage = $singleUnitFinalPrice * (1 - $bulkDiscountPercentage);

        $totalPackagePrice = round($pricePerUnitInPackage * $qty, 2);

        $variant->packages()->create([
          'quantity' => $qty,
          'price' => $totalPackagePrice,
        ]);
      }
    });
  }
}
