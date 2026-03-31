<?php

namespace App\Filament\Resources\CartResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
      ->columns([
        TextColumn::make('productVariant.product.name')
          ->label('المنتج'),

        TextColumn::make('quantity')
          ->label('الكمية'),

        TextColumn::make('productVariant.color.color')
          ->label('اللون')
          ->placeholder('-'),

        TextColumn::make('productVariant.size.size')
          ->label('الحجم')
          ->placeholder('-'),

        TextColumn::make('productVariant.material.material')
          ->label('المادة')
          ->placeholder('-'),

        TextColumn::make('productVariant.price')
          ->label('السعر الأصلي')
          ->money('USD', locale: 'en_US'),

        TextColumn::make('productVariant.discount')
          ->label('الخصم')
          ->suffix('%')
          ->afterStateUpdated(fn($set, $get) => self::updateTotal($set, $get))
          ->placeholder('0%'),

        TextColumn::make('productVariant.final_price')
          ->label('السعر بعد الخصم')
          ->getStateUsing(fn($record) => round($record->productVariant->price * (1 - $record->productVariant->discount / 100), 2))
          ->money('USD', locale: 'en_US'),

        TextColumn::make('total_price')
          ->label('الإجمالي')
          ->getStateUsing(fn($record) => $record->quantity * round($record->productVariant->price * (1 - $record->productVariant->discount / 100), 2))
          ->money('USD', locale: 'en_US'),

        TextColumn::make('created_at')
          ->label('أضيف بتاريخ')
          ->since(),
      ])
      ->filters([])
      ->actions([])
      ->headerActions([])
      ->bulkActions([]);
  }


}