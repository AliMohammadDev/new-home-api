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
          ->label('المنتج'),

        TextColumn::make('quantity')
          ->label('الكمية')
          ->formatStateUsing(fn($state) => $state),

        TextColumn::make('price')
          ->label('السعر')
          ->formatStateUsing(fn($state) => number_format($state, 2, '.', ','))
          ->money(),

        TextColumn::make('total')
          ->label('المجموع')
          ->formatStateUsing(fn($state) => number_format($state, 2, '.', ','))
          ->money(),
      ])
      ->actions([])
      ->headerActions([])
      ->bulkActions([]);
  }
}
