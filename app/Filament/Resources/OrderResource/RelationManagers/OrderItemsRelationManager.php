<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\Summarizers\Summarizer;

class OrderItemsRelationManager extends RelationManager
{
  protected static string $relationship = 'orderItems';
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
            return $variant->images->map(fn($img) => str_contains($img->image, 'product_variants/') ? $img->image : "product_variants/{$variant->id}/{$img->image}")->toArray();
          })
          ->disk('public'),

        TextColumn::make('productVariant.product.name')
          ->label('المنتج')
          ->getStateUsing(function ($record) {
            $name = $record->productVariant?->product?->name;
            return is_array($name) ? ($name[app()->getLocale()] ?? $name['ar'] ?? array_values($name)[0]) : $name;
          })
          ->description(fn($record) => "SKU: " . ($record->productVariant?->sku ?? '-')),

        TextColumn::make('productVariant.barcode')
          ->label('باركود')
          ->icon('heroicon-m-qr-code')
          ->color('gray')
          ->copyable()
          ->searchable(),

        TextColumn::make('productVariant.price')
          ->label('السعر')
          ->money('USD', locale: 'en_US')
          ->color('gray')
          ->alignCenter()
          ->extraAttributes(['style' => 'text-decoration: line-through;']),

        TextColumn::make('productVariant.discount')
          ->label('الخصم')
          ->formatStateUsing(fn($state) => fmod($state, 1) == 0 ? (int) $state : $state)
          ->suffix('%')
          ->badge()
          ->color('danger')
          ->alignCenter(),

        TextColumn::make('unit_price')
          ->label('صافي السعر')
          ->getStateUsing(fn($record) => $record->productVariant?->final_price)
          ->money('USD', locale: 'en_US')
          ->alignCenter()
          ->color('success')
          ->weight('bold'),

        TextColumn::make('quantity')
          ->label('الكمية')
          ->badge()
          ->color('info')
          ->alignCenter(),

        TextColumn::make('total')
          ->label('المجموع الفرعي')
          ->alignCenter()
          ->getStateUsing(fn($record) => $record->quantity * ($record->productVariant?->final_price ?? 0))
          ->money('USD', locale: 'en_US')
          ->color('success')
          ->weight('bold')
          ->summarize([
            Summarizer::make()
              ->using(function ($livewire) {
                $records = $livewire->getRelationship()->get();

                $productsTotal = $records->sum(
                  fn($item) => (float) ($item->quantity * ($item->productVariant?->final_price ?? 0))
                );

                $order = $this->getOwnerRecord();
                $shipping = (float) ($order->shipping_fee ?? 0);
                $delivery = (float) ($order->delivery_fee ?? 0);

                $grandTotal = $productsTotal + $shipping + $delivery;

                $formattedTotal = number_format($grandTotal, 2);

                return new \Illuminate\Support\HtmlString("
                <div class='text-base font-bold text-success-600 px-3 py-1 border-t-2 border-gray-100'>
                    الإجمالي الكلي: $ {$formattedTotal}
                </div>
            ");
              })
          ])
      ])
      ->headerActions([
        Tables\Actions\Action::make('print_invoice')
          ->label('طباعة الفاتورة')
          ->icon('heroicon-m-printer')
          ->color('success')
          ->url(fn() => route('orders.print', $this->getOwnerRecord()->id))
          ->openUrlInNewTab(),
      ])
      ->actions([
        Tables\Actions\Action::make('view_variant')
          ->label('خيارات المنتج')
          ->icon('heroicon-m-adjustments-horizontal')
          ->color('info')
          ->url(fn($record): string => "/admin/product-variants/{$record->product_variant_id}/edit")
          ->openUrlInNewTab()
          ->visible(fn() => auth()->user()->hasRole('super_admin')),
      ]);
  }
}
