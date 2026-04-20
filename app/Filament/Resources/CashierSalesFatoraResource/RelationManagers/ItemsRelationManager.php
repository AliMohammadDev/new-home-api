<?php

namespace App\Filament\Resources\CashierSalesFatoraResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
  protected static string $relationship = 'items';

  protected static ?string $title = 'المنتجات المباعة في هذه الفاتورة';

  public function form(Form $form): Form
  {
    return $form->schema([
    ]);
  }

  public function table(Table $table): Table
  {
    return $table
      ->recordTitleAttribute('id')
      ->columns([
        Tables\Columns\TextColumn::make('variant.product.name')
          ->label('المنتج')
          ->state(fn($record) => $record->variant->product->name['ar'] ?? 'غير مسمى'),

        Tables\Columns\TextColumn::make('variant.sku')
          ->label('SKU')
          ->badge(),

        Tables\Columns\TextColumn::make('quantity')
          ->label('الكمية')
          ->badge()
          ->color('info'),

        Tables\Columns\TextColumn::make('price')
          ->label('سعر الوحدة')
          ->money('USD', locale: 'en_US'),

        Tables\Columns\TextColumn::make('variant.discount')
          ->label('الخصم')
          ->formatStateUsing(fn($state) => fmod($state, 1) == 0 ? (int) $state : $state)
          ->suffix('%'),

        Tables\Columns\TextColumn::make('full_price')
          ->label('الإجمالي بعد الخصم')
          ->money('USD', locale: 'en_US')
          ->summarize(
            Tables\Columns\Summarizers\Sum::make()
              ->label('المجموع')
              ->money('USD', locale: 'en_US')
          ),
      ])
      ->filters([])
      ->headerActions([])
      ->actions([
      ])
      ->bulkActions([]);
  }
}
