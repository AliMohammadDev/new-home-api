<?php

namespace App\Filament\Resources\WarehouseResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class ProductVariantsRelationManager extends RelationManager
{
  protected static string $relationship = 'productVariants';
  protected static ?string $title = 'المنتجات المتوفرة في المستودع';

  public function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\TextInput::make('sku')
          ->required()
          ->maxLength(255),
      ]);
  }

  public function table(Table $table): Table
  {
    return $table
      ->recordTitleAttribute('sku')
      ->columns([
        Tables\Columns\ImageColumn::make('variant_images')
          ->label('صور المنتج')
          ->circular()
          ->stacked()
          ->getStateUsing(function ($record) {
            if (!$record->images || $record->images->isEmpty()) {
              return null;
            }
            return $record->images->map(function ($img) use ($record) {
              return str_contains($img->image, 'product_variants/')
                ? $img->image
                : "product_variants/{$record->id}/{$img->image}";
            })->toArray();
          })
          ->disk('public'),
        Tables\Columns\TextColumn::make('product.name')
          ->label('المنتج')
          ->formatStateUsing(fn($record) => $record->product?->getTranslatedNameAttribute() ?? '-')
          ->searchable(query: function ($query, string $search) {
            $query->whereHas(
              'product',
              fn($q) =>
              $q->where('name->ar', 'like', "%{$search}%")
                ->orWhere('name->en', 'like', "%{$search}%")
            );
          })
          ->description(fn($record) => "SKU: " . $record->sku),

        Tables\Columns\TextColumn::make('barcode')
          ->label('الباركود')
          ->formatStateUsing(fn($state) => $state ? new HtmlString(
            "<div class='flex flex-col items-center justify-center gap-1'>" .
            \DNS1D::getBarcodeHTML((string) $state, 'C128', 1.2, 22) .
            "<span class='text-[10px] font-mono'>$state</span></div>"
          ) : '-')
          ->html()
          ->alignCenter(),

        Tables\Columns\TextColumn::make('price')
          ->label('السعر الأصلي')
          ->money('USD', locale: 'en_US')
          ->sortable()
          ->color('gray')
          ->description('السعر قبل الخصم'),


        Tables\Columns\TextColumn::make('discount')
          ->label('الخصم')
          ->formatStateUsing(fn($state) => number_format($state, 0) . '%')
          ->suffix('%')
          ->badge()
          ->color(fn($state) => $state > 0 ? 'danger' : 'gray')
          ->sortable(),


        Tables\Columns\TextColumn::make('final_price')
          ->label('السعر النهائي')
          ->getStateUsing(fn($record) => $record->final_price)
          ->money('USD', locale: 'en_US')
          ->weight('bold')
          ->color('success')
          ->description('السعر بعد تطبيق الخصم'),

        Tables\Columns\TextColumn::make('specs')
          ->label('المواصفات')
          ->getStateUsing(function ($record) {
            return "{$record->color?->color} / {$record->size?->size}";
          })
          ->color('gray')
          ->size('sm'),

        Tables\Columns\TextColumn::make('pivot.amount')
          ->label('الكمية المتوفرة')
          ->badge()
          ->color(fn($state) => $state > 10 ? 'success' : 'danger')
          ->sortable(),

        Tables\Columns\TextColumn::make('pivot.arrival_time')
          ->label('وقت الوصول المتوقع')
          ->icon('heroicon-m-clock'),
      ])
      ->filters([])
      ->headerActions([

      ])
      ->actions([

        Tables\Actions\DetachAction::make()
          ->label('إزالة من المستودع')
          ->before(function ($record) {
            $amountToReturn = (int) $record->pivot->amount;
            if ($amountToReturn > 0) {
              $record->increment('stock_quantity', $amountToReturn);
            }
          }),
      ]);
  }
}
