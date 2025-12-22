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

        TextColumn::make('productVariant.product.price')
          ->label('السعر الأصلي')
          ->money('USD'),

        TextColumn::make('productVariant.product.discount')
          ->label('الخصم')
          ->suffix('%')
          ->placeholder('0%'),

        TextColumn::make('final_price')
          ->label('السعر بعد الخصم')
          ->getStateUsing(fn($record) => round($record->productVariant->product->price * (1 - $record->productVariant->product->discount / 100), 2))
          ->money('USD'),

        TextColumn::make('total_price')
          ->label('الإجمالي')
          ->getStateUsing(fn($record) => $record->quantity * round($record->productVariant->product->price * (1 - $record->productVariant->product->discount / 100), 2))
          ->money('USD'),

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
