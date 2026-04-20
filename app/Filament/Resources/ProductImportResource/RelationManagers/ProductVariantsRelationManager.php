<?php

namespace App\Filament\Resources\ProductImportResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use App\Models\ProductVariant;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class ProductVariantsRelationManager extends RelationManager
{
  protected static string $relationship = 'productVariants';
  protected static ?string $title = 'البضائع المستوردة من هذا المورد';

  public function table(Table $table): Table
  {
    return $table
      ->modifyQueryUsing(fn(Builder $query) => $query->with(['product', 'color', 'size', 'material']))
      ->columns([
        Tables\Columns\TextColumn::make('product.name')
          ->label('المنتج')
          ->getStateUsing(fn($record) => $record->product?->name[app()->getLocale()] ?? $record->product?->name['en'] ?? '')
          ->description(fn($record) => "SKU: " . $record->sku)
          ->searchable(),

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
          ->color('gray')
          ->sortable(),

        Tables\Columns\TextColumn::make('discount')
          ->label('الخصم')
          ->formatStateUsing(fn($state) => number_format((float) $state, 0) . '%')
          ->badge()
          ->color(fn($state) => $state > 0 ? 'danger' : 'gray')
          ->sortable(),

        Tables\Columns\TextColumn::make('final_price')
          ->label('السعر النهائي')
          ->getStateUsing(fn($record) => $record->final_price)
          ->money('USD', locale: 'en_US')
          ->weight('bold')
          ->color('success'),


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
          ->color('info')
          ->url(fn(ProductVariant $record) => route('supplier.print', ['ids' => [$record->id]]))
          ->openUrlInNewTab(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([

          Tables\Actions\BulkAction::make('print_selected')
            ->label('طباعة المحدد كـ PDF')
            ->icon('heroicon-m-printer')
            ->color('success')
            ->action(function (\Illuminate\Database\Eloquent\Collection $records, $livewire) {
              $ids = $records->pluck('id')->toArray();
              $url = route('supplier.print', ['ids' => $ids]);

              $livewire->js("window.open('{$url}', '_blank')");
            }),

        ]),
      ]);
  }
}