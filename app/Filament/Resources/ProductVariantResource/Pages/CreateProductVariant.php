<?php



namespace App\Filament\Resources\ProductVariantResource\Pages;

use App\Filament\Resources\ProductVariantResource;
use App\Models\ProductVariant;
use App\Models\ProductVariantImage;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class CreateProductVariant extends CreateRecord
{
  protected static string $resource = ProductVariantResource::class;

  protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
  {
    $productId = $data['product_id'];
    $lastRecord = null;

    foreach ($data['variants'] as $variantData) {
      $variant = ProductVariant::create([
        'product_id' => $productId,
        'color_id' => $variantData['color_id'],
        'size_id' => $variantData['size_id'],
        'material_id' => $variantData['material_id'],
        'price' => $variantData['price'],
        'discount' => $variantData['discount'] ?? 0,
        'stock_quantity' => $variantData['stock_quantity'],
        'image' => '',
      ]);

      if (!empty($variantData['packages'])) {
        foreach ($variantData['packages'] as $packageData) {
          $variant->packages()->create([
            'quantity' => $packageData['quantity'],
            'price' => $packageData['price'],
          ]);
        }
      }

      if (!empty($variantData['images'])) {

        $variantDirectory = "product_variants/{$variant->id}";

        foreach ($variantData['images'] as $index => $tempPath) {
          $filename = Str::uuid() . '.webp';
          $finalPath = "{$variantDirectory}/{$filename}";

          if (!Storage::disk('public')->exists($variantDirectory)) {
            Storage::disk('public')->makeDirectory($variantDirectory);
          }


          $img = Image::make(Storage::disk('public')->path($tempPath))
            ->resize(1000, 1000, function ($constraint) {
              $constraint->aspectRatio();
              $constraint->upsize();
            })
            ->encode('webp', 70);

          Storage::disk('public')->put($finalPath, $img);

          ProductVariantImage::create([
            'product_variant_id' => $variant->id,
            'image' => $filename,
          ]);

          if ($index === 0) {
            $variant->update(['image' => $filename]);
          }

          Storage::disk('public')->delete($tempPath);
        }
      }
      $lastRecord = $variant;
    }

    return $lastRecord;
  }

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}