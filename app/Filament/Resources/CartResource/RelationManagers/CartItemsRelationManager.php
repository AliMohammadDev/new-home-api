<?php

namespace App\Filament\Resources\CartResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CartItemsRelationManager extends RelationManager
{
  protected static string $relationship = 'cartItems';
  protected static ?string $title = 'محتويات السلة';

  public function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\TextInput::make('cartItems')
          ->required()
          ->maxLength(255),
      ]);
  }

  public function table(Table $table): Table
  {
    return $table

      ->modifyQueryUsing(fn(Builder $query) => $query->with([
        'productVariant.product',
        'productVariant.color',
        'productVariant.size',
        'productVariant.material',
      ]))

      ->columns([
        TextColumn::make('productVariant.product.name')
          ->label('المنتج')
          ->getStateUsing(fn($record) => $record->productVariant->product->name[app()->getLocale()] ?? $record->productVariant->product->name['en'])
          ->description(fn($record) => "SKU: " . $record->productVariant->sku)
          ->searchable(query: function (Builder $query, string $search): Builder {
            return $query->whereHas('productVariant.product', function (Builder $q) use ($search) {
              $q->where('name->' . app()->getLocale(), 'like', "%{$search}%")
                ->orWhere('name->en', 'like', "%{$search}%");
            });
          }),

        TextColumn::make('quantity')
          ->label('الكمية')
          ->badge(),

        Tables\Columns\ColorColumn::make('productVariant.color.hex_code')
          ->label('اللون')
          ->placeholder('-')
          ->copyable()
          ->copyMessage('تم نسخ كود اللون'),

        TextColumn::make('productVariant.size.size')
          ->label('الحجم')
          ->placeholder('-'),

        TextColumn::make('productVariant.material.material')
          ->label('المادة')
          ->placeholder('-'),

        TextColumn::make('productVariant.price')
          ->label('السعر الأصلي')
          ->money('USD', locale: 'en_US')
          ->color('gray')
          ->size('sm'),

        TextColumn::make('productVariant.discount')
          ->label('الخصم')
          ->formatStateUsing(fn($state) => number_format((float) $state, 0, '.', '') . '%')
          ->badge()
          ->color('danger')
          ->placeholder('0%'),

        TextColumn::make('productVariant.final_price')
          ->label('السعر بعد الخصم')
          ->getStateUsing(fn($record) => round($record->productVariant->price * (1 - $record->productVariant->discount / 100), 2))
          ->money('USD', locale: 'en_US'),

        TextColumn::make('total_price')
          ->label('الإجمالي')
          ->getStateUsing(fn($record) => $record->quantity * round($record->productVariant->price * (1 - $record->productVariant->discount / 100), 2))
          ->money('USD', locale: 'en_US'),

        TextColumn::make('productVariant.final_price')
          ->label('السعر بعد الخصم')
          ->money('USD', locale: 'en_US'),

        TextColumn::make('total_price')
          ->label('الإجمالي')
          ->getStateUsing(fn($record) => $record->quantity * $record->productVariant->final_price)
          ->money('USD', locale: 'en_US')
          ->weight('bold')
          ->color('success'),

        TextColumn::make('created_at')
          ->label('أضيف بتاريخ')
          ->since(),
      ])
      ->filters([

        Tables\Filters\SelectFilter::make('product')
          ->label('تصفية حسب المنتج')
          ->relationship('productVariant.product', 'id')
          ->getOptionLabelFromRecordUsing(fn(Model $record) => $record->name[app()->getLocale()] ?? $record->name['en'])
          ->searchable()
          ->preload(),

        Tables\Filters\SelectFilter::make('color')
          ->relationship('productVariant.color', 'color')
          ->label('اللون'),

      ])
      ->actions([])
      ->headerActions([])
      ->bulkActions([]);
  }


}
