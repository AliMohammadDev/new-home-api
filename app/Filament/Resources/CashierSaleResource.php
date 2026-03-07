<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashierSaleResource\Pages;
use App\Models\CashierSale;
use App\Models\ProductVariant;
use App\Models\SalesPointCashier;
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
            ->required()
            ->searchable(['sku'])
            ->preload()
            ->live()
            ->options(function (Forms\Get $get) {
              $cashierId = $get('sales_point_cashier_id');

              if (!$cashierId) {
                return [];
              }

              $cashier = SalesPointCashier::with('salesPoint.warehouse')
                ->find($cashierId);

              if (!$cashier || !$cashier->salesPoint || !$cashier->salesPoint->warehouse) {
                return [];
              }

              return $cashier->salesPoint->warehouse->productVariants()
                ->get()
                ->mapWithKeys(function ($variant) {
                  $productName = $variant->product->name['ar'] ?? 'منتج غير مسمى';
                  $sku = $variant->sku ?? 'بدون SKU';
                  $stock = $variant->pivot->amount ?? 0;

                  return [$variant->id => "{$productName} - {$sku} (متوفر: {$stock})"];
                });
            })
            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
              if (!$state)
                return;

              $variant = ProductVariant::find($state);
              $variant = \App\Models\ProductVariant::find($state);

              if ($variant) {
                $set('price', $variant->price);
                $currentQuantity = (float) ($get('quantity') ?? 1);
                $set('full_price', (float) $variant->price * $currentQuantity);
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

            })
            ->rules([
              function (Forms\Get $get) {
                return function (string $attribute, $value, $fail) use ($get) {
                  $variantId = $get('product_variant_id');
                  $cashierId = $get('sales_point_cashier_id');

                  if ($variantId && $cashierId) {
                    $cashier = SalesPointCashier::find($cashierId);
                    $warehouse = $cashier->salesPoint->warehouse;
                    $stock = $warehouse->productVariants()
                      ->where('product_variant_id', $variantId)
                      ->first()?->pivot->amount ?? 0;

                    if ($value > $stock) {
                      $fail("الكمية المطلوبة غير متوفرة. المتاح في المستودع: {$stock}");
                    }
                  }
                };
              },
            ]),


          Forms\Components\TextInput::make('price')
            ->label('سعر الوحدة')
            ->numeric()
            ->required()
            ->prefix('USD')
            ->disabled()
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
