<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\Summarizers\Sum;

class OrderItemsRelationManager extends RelationManager
{
  protected static string $relationship = 'OrderItems';
  protected static ?string $title = 'محتويات السلة (المنتجات)';

  public function table(Table $table): Table
  {
    return $table
      ->recordTitleAttribute('id')
      ->columns([
        Tables\Columns\ImageColumn::make('product_images')
          ->label('صور المنتج')
          ->circular()
          ->stacked()
          ->getStateUsing(function ($record) {
            $variant = $record->productVariant;
            if (!$variant || !$variant->images)
              return null;

            return $variant->images->map(function ($img) use ($variant) {
              $imageName = $img->image;
              return str_contains($imageName, 'product_variants/')
                ? $imageName
                : "product_variants/{$variant->id}/{$imageName}";
            })->toArray();
          })
          ->disk('public')
          ->grow(false),

        TextColumn::make('productVariant.product.name')
          ->label('المنتج')
          ->description(fn($record) => "SKU: " . ($record->productVariant->sku ?? '-'))
          ->getStateUsing(function ($record) {
            $name = $record->productVariant?->product?->name;
            return is_array($name) ? ($name[app()->getLocale()] ?? $name['ar'] ?? array_values($name)[0]) : $name;
          })
          ->searchable(),

        TextColumn::make('price')
          ->label('السعر الإفرادي')
          ->money('USD', locale: 'en_US')
          ->sortable()
          ->alignCenter(),

        TextColumn::make('quantity')
          ->label('الكمية')
          ->badge()
          ->color('info')
          ->alignCenter(),

        TextColumn::make('total')
          ->label('المجموع الفرعي')
          ->getStateUsing(fn($record) => $record->quantity * $record->price)
          ->money('USD', locale: 'en_US')
          ->color('success')
          ->weight('bold')
          ->summarize(
            Sum::make()->label('إجمالي الفاتورة')
              ->money('USD', locale: 'en_US')
          ),
      ])
      ->headerActions([
        // زر طباعة الفاتورة في سطر العنوان
        Tables\Actions\Action::make('print_invoice')
          ->label('طباعة الفاتورة')
          ->icon('heroicon-m-printer')
          ->color('success')
          // هنا نضع الرابط الذي يولد الفاتورة، نمرر له معرف الطلب (Order ID)
          ->url(fn() => route('orders.print', $this->getOwnerRecord()->id))
          ->openUrlInNewTab(),
      ])
      ->actions([
        Tables\Actions\Action::make('view_variant')
          ->label('عرض خيارات المنتج')
          ->icon('heroicon-m-adjustments-horizontal')
          ->color('info')
          ->url(fn($record): string => "/admin/product-variants/{$record->product_variant_id}/edit")
          ->openUrlInNewTab(),

        Tables\Actions\Action::make('view_product')
          ->label('عرض المنتج')
          ->icon('heroicon-m-eye')
          ->color('gray')
          ->url(fn($record): string => "/admin/products/{$record->productVariant?->product_id}/edit")
          ->openUrlInNewTab(),
      ])
      ->bulkActions([]);
  }
}
