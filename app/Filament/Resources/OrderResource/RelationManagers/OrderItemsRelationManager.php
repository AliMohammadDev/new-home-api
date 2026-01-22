<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderItemsRelationManager extends RelationManager
{
  protected static string $relationship = 'OrderItems';
  protected static ?string $title = 'تفاصيل الطلب';


  public function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\TextInput::make('OrderItems')
          ->required()
          ->maxLength(255),
      ]);
  }

  public function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('productVariant.product.name')
          ->label('المنتج')
          ->getStateUsing(function ($record) {
            $product = $record->productVariant?->product;

            if (!$product) {
              return '-';
            }
            $name = $product->name;
            if (is_array($name)) {
              return $name[app()->getLocale()] ?? $name['en'] ?? array_values($name)[0] ?? '-';
            }
            return $name ?? '-';
          })
          ->searchable()
          ->sortable(),

        TextColumn::make('quantity')
          ->label('الكمية')
          ->formatStateUsing(fn($state) => $state),

      ])
      ->actions([])
      ->headerActions([])
      ->bulkActions([]);
  }
}