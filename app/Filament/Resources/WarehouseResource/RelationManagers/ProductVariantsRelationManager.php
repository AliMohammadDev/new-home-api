<?php

namespace App\Filament\Resources\WarehouseResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
          ->getStateUsing(fn($record) => $record->product->name['ar'] ?? '')
          ->searchable(),

        Tables\Columns\TextColumn::make('sku')
          ->label('رمز المنتج (SKU)')
          ->copyable()
          ->searchable(),

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