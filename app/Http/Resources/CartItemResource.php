<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    $unitPrice = ($this->product_variant_package_id && $this->productVariantPackage)
      ? $this->productVariantPackage->price
      : ($this->productVariant ? $this->productVariant->final_price : 0);

    return [
      'id' => $this->id,

      'image' => $this->productVariant?->image
        ? asset('storage/product_variants/' . $this->productVariant->id . '/' . $this->productVariant->image)
        : ($this->productVariant?->images?->first()
          ? asset('storage/product_variants/' . $this->productVariant->id . '/' . $this->productVariant->images->first()->image)
          : null),

      'quantity' => $this->quantity,
      'type' => $this->product_variant_package_id ? 'Package' : 'Individual',

      'package_info' => $this->when($this->product_variant_package_id && $this->productVariantPackage, function () {
        return [
          'id' => $this->productVariantPackage->id,
          'quantity_in_package' => $this->productVariantPackage->quantity,
          'package_price' => $this->productVariantPackage->price,
        ];
      }),

      'product_variant' => [
        'id' => $this->productVariant?->id,
        'product_id' => $this->productVariant?->product?->id,
        'name' => $this->productVariant?->product?->translated_name ?? 'N/A',
        'price' => $this->productVariant?->price,
        'discount' => $this->productVariant?->discount,
        'sku' => $this->productVariant?->sku,
        'final_price' => $this->productVariant?->final_price,
        'color_name' => $this->productVariant?->color?->color,
        'color_code' => $this->productVariant?->color?->hex_code,
        'size' => $this->productVariant?->size?->size,
        'material' => $this->productVariant?->material?->translated_material,
      ],

      'available_options' => $this->productVariant?->product?->variants
        ? $this->productVariant->product->variants->groupBy('color_id')->map(function ($colorGroup) {
          $color = $colorGroup->first()->color;
          return [
            'id' => $color?->id,
            'name' => $color?->color,
            'hex' => $color?->hex_code,
            'available_sizes' => $colorGroup->groupBy('size_id')->map(function ($sizeGroup) {
              $size = $sizeGroup->first()->size;
              return [
                'id' => $size?->id,
                'name' => $size?->size,
                'available_materials' => $sizeGroup->map(function ($variant) {
                  return [
                    'id' => $variant->material?->id,
                    'name' => $variant->material?->translated_material,
                    'stock' => $variant->stock_quantity,
                    'variant_id' => $variant->id,
                    'price' => $variant->price,
                    'final_price' => $variant->final_price,
                    'sku' => $variant->sku,

                    'available_packages' => $variant->packages->map(function ($package) {
                      return [
                        'id' => $package->id,
                        'quantity' => $package->quantity,
                        'price' => $package->price,
                      ];
                    })->values(),
                  ];
                })->values(),
              ];
            })->values(),
          ];
        })->values()
        : [],

      'total_price' => round($unitPrice * $this->quantity, 2),
    ];
  }
}
