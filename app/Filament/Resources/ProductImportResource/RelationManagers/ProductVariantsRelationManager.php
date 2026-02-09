<?php

namespace App\Filament\Resources\ProductImportResource\RelationManagers;

use App\Models\ProductVariant;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ProductVariantsRelationManager extends RelationManager
{
  protected static string $relationship = 'productVariants';
  protected static ?string $title = 'البضائع المستوردة من هذا المورد';

  public function table(Table $table): Table
  {
    return $table
      ->recordTitleAttribute('sku')
      ->columns([
        Tables\Columns\TextColumn::make('product.name')
          ->label('المنتج')
          ->getStateUsing(fn($record) => $record->product?->name[app()->getLocale()] ?? $record->product?->name['en'] ?? '')
          ->searchable(),


        Tables\Columns\ColorColumn::make('color.hex_code')
          ->label('اللون')
          ->sortable(),

        Tables\Columns\TextColumn::make('size.size')->label('الحجم'),
        Tables\Columns\TextColumn::make('material.material')->label('المادة'),

        Tables\Columns\TextColumn::make('stock_quantity')
          ->label('الكمية المستوردة')
          ->badge()
          ->color('success')
          ->sortable(),


      ])
      ->filters([])
      ->headerActions([])
      ->actions([
        Tables\Actions\Action::make('print_single')
          ->label('طباعة')
          ->icon('heroicon-s-printer')
          ->color('gray')
          ->url(fn(ProductVariant $record) => route('supplier.print', ['ids' => [$record->id]]))
          ->openUrlInNewTab(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\BulkAction::make('print_selected')
            ->label('طباعة المحدد كـ PDF')
            ->icon('heroicon-m-printer')
            ->color('success')
            ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
              return redirect()->route('supplier.print', [
                'ids' => $records->pluck('id')->toArray()
              ]);
            }),
        ]),
      ]);
  }
}
