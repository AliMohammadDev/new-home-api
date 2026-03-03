<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashierSaleResource\Pages;
use App\Models\CashierSale;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CashierSaleResource extends Resource
{
  protected static ?string $model = CashierSale::class;
  protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
  protected static ?string $navigationGroup = 'إدارة المبيعات';
  protected static ?int $navigationSort = 5;
  protected static ?string $navigationLabel = 'مبيعات الكاشير (الأصناف)';
  protected static ?string $pluralModelLabel = 'مبيعات الكاشير';
  protected static ?string $modelLabel = 'مبيع جديد';


  public static function form(Form $form): Form
  {
    return $form->schema([
      Forms\Components\Section::make('تفاصيل المادة المباعة')
        ->schema([
          Forms\Components\Select::make('sales_point_cashier_id')
            ->label('الكاشير')
            ->relationship('cashier', 'id')
            ->getOptionLabelFromRecordUsing(fn($record) => $record->user?->name ?? 'غير محدد')
            ->searchable()
            ->preload()
            ->required(),

          Forms\Components\Select::make('product_variant_id')
            ->label('المنتج (الخيار)')
            ->relationship('variant', 'sku')
            ->getOptionLabelFromRecordUsing(function ($record) {

              $productName = $record->product->name['ar'] ?? 'منتج غير مسمى';
              $sku = $record->sku ?? 'بدون SKU';

              return "{$productName} - {$sku}";
            })
            ->searchable(['sku'])
            ->preload()
            ->required()
            ->live()
            ->afterStateUpdated(function ($state, Forms\Set $set) {
              if (!$state)
                return;
              $variant = \App\Models\ProductVariant::find($state);
              if ($variant) {
                $set('price', $variant->price);
                $set('full_price', (float) $variant->price * 1);
              }
            }),
          Forms\Components\TextInput::make('quantity')
            ->label('الكمية')
            ->numeric()
            ->default(1)
            ->required()
            ->live(onBlur: true)
            ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
              $price = (float) ($get('price') ?? 0);
              $set('full_price', (float) $state * $price);
            }),

          Forms\Components\TextInput::make('price')
            ->label('سعر الوحدة')
            ->numeric()
            ->required()
            ->prefix('USD')
            ->live(onBlur: true)
            ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
              $quantity = (float) ($get('quantity') ?? 0);
              $set('full_price', (float) $state * $quantity);
            }),

          Forms\Components\TextInput::make('full_price')
            ->label('الإجمالي')
            ->numeric()
            ->readonly()
            ->prefix('USD'),


        ])->columns(2),
    ]);
  }


  public static function table(Table $table): Table
  {
    return $table->columns([
      Tables\Columns\TextColumn::make('fatora.id')->label('رقم الفاتورة')->sortable(),
      Tables\Columns\TextColumn::make('variant.product.name')->label('المنتج')->searchable(),
      Tables\Columns\TextColumn::make('quantity')->label('الكمية'),
      Tables\Columns\TextColumn::make('price')->label('السعر')->money('USD', locale: 'en_US'),
      Tables\Columns\TextColumn::make('full_price')->label('الإجمالي')
        ->money('USD', locale: 'en_US')
        ->summarize(
          Tables\Columns\Summarizers\Sum::make()
            ->label('المجموع الكلي')
            ->money('USD', locale: 'en_US')
        ),
      Tables\Columns\TextColumn::make('cashier.user.name')->label('الكاشير'),
    ])
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make(),
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
      'index' => Pages\ListCashierSales::route('/'),
      'create' => Pages\CreateCashierSale::route('/create'),
      'edit' => Pages\EditCashierSale::route('/{record}/edit'),
    ];
  }



}
