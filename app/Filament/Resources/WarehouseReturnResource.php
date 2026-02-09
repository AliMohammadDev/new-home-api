<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WarehouseReturnResource\Pages;
use App\Models\WarehouseReturn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class WarehouseReturnResource extends Resource
{
  protected static ?string $model = WarehouseReturn::class;

  protected static ?string $navigationIcon = 'heroicon-o-arrow-uturn-left';
  protected static ?string $navigationLabel = 'المرتجعات من المستودعات';
  protected static ?string $pluralModelLabel = 'المرتجعات';
  protected static ?string $modelLabel = 'مرتجع';
  protected static ?string $navigationGroup = 'شحن و استيراد';


  public static function canCreate(): bool
  {
    return false;
  }

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make('تفاصيل المرتجع')
          ->schema([
            Forms\Components\Select::make('warehouse_id')
              ->relationship('warehouse', 'name')
              ->label('المستودع المصدر')
              ->disabled(),
            Forms\Components\Select::make('product_variant_id')
              ->relationship('productVariant', 'sku')
              ->label('المنتج (SKU)')
              ->disabled(),
            Forms\Components\TextInput::make('amount')
              ->label('الكمية المرجعة')
              ->numeric()
              ->disabled(),
            Forms\Components\Textarea::make('reason')
              ->label('سبب الإرجاع')
              ->columnSpanFull()
              ->disabled(),
          ])->columns(2)
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        ImageColumn::make('productVariant.images')
          ->label('الصورة')
          ->circular()
          ->stacked()
          ->getStateUsing(function ($record) {
            $variant = $record->productVariant;
            if (!$variant || !$variant->images)
              return null;
            return $variant->images->map(
              fn($img) =>
              str_contains($img->image, 'product_variants/')
              ? $img->image
              : "product_variants/{$variant->id}/{$img->image}"
            )->toArray();
          }),

        TextColumn::make('productVariant.product.name')
          ->label('المنتج')
          ->getStateUsing(fn($record) => $record->productVariant?->product?->name['ar'] ?? 'غير معروف')
          ->searchable(),

        TextColumn::make('productVariant.sku')
          ->label('SKU')
          ->copyable()
          ->searchable(),

        TextColumn::make('warehouse.name')
          ->label('من مستودع')
          ->badge()
          ->color('info'),

        TextColumn::make('amount')
          ->label('الكمية المرجعة')
          ->badge()
          ->color('warning')
          ->alignCenter(),

        TextColumn::make('reason')
          ->label('السبب')
          ->placeholder('لم يتم ذكر سبب')
          ->limit(30),

        TextColumn::make('created_at')
          ->label('تاريخ الإرجاع')
          ->dateTime('Y-m-d H:i')
          ->sortable(),
      ])
      ->defaultSort('created_at', 'desc')
      ->filters([
        Tables\Filters\SelectFilter::make('warehouse_id')
          ->label('حسب المستودع')
          ->relationship('warehouse', 'name'),
      ])
      ->actions([
        Tables\Actions\ViewAction::make()
          ->label('عرض التفاصيل'),
        Tables\Actions\DeleteAction::make()
          ->label('حذف السجل'),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ]);
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListWarehouseReturns::route('/'),
    ];
  }
}