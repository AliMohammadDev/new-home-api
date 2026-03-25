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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;

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
            ->relationship(
              name: 'cashier',
              titleAttribute: 'id',
              modifyQueryUsing: fn(Builder $query) => $query->whereHas(
                'user',
                fn(Builder $userQuery) => $userQuery->role('sales_point_cashier')
              )
            )
            ->getOptionLabelFromRecordUsing(fn($record) => $record->user?->name ?? 'غير محدد')
            ->default(fn() => SalesPointCashier::where('user_id', auth()->id())->value('id'))
            ->disabled(fn() => auth()->check() && auth()->user()->hasRole('sales_point_cashier'))
            ->dehydrated()
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
              if ($variant) {
                $price = (float) $variant->price;
                $discountPercent = (float) ($variant->discount ?? 0);
                $quantity = (float) ($get('quantity') ?? 1);

                $set('price', $price);
                $set('discount', $discountPercent);

                $total = ($price * $quantity) * (1 - ($discountPercent / 100));
                $set('full_price', round($total, 2));
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
              $discountPercent = (float) ($get('discount') ?? 0);
              $quantity = (float) ($state ?? 0);

              $total = ($price * $quantity) * (1 - ($discountPercent / 100));
              $set('full_price', round($total, 2));
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
            ->dehydrated()
            ->live(onBlur: true)
            ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
              $quantity = (float) ($get('quantity') ?? 0);
              $discountPercent = (float) ($get('discount') ?? 0);
              $total = ($state * $quantity) * (1 - ($discountPercent / 100));
              $set('full_price', round($total, 2));
            }),

          Forms\Components\TextInput::make('discount')
            ->label('الخصم')
            ->numeric()
            ->default(0)
            ->disabled()
            ->suffix('%')
            ->dehydrated()
            ->live(onBlur: true)
            ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
              $price = (float) ($get('price') ?? 0);
              $quantity = (float) ($get('quantity') ?? 1);
              $discountPercent = (float) ($state ?? 0);
              $total = ($price * $quantity) * (1 - ($discountPercent / 100));
              $set('full_price', round($total, 2));
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
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('fatora.id')->label('رقم الفاتورة')->sortable(),

        Tables\Columns\TextColumn::make('variant.product.name')
          ->label('المنتج')
          ->getStateUsing(fn($record) => $record->variant?->product?->name[App::getLocale()] ?? $record->variant?->product?->name['en'] ?? '')
          ->searchable(query: function (Builder $query, string $search): Builder {
            return $query->whereHas('variant.product', function (Builder $q) use ($search) {
              $locale = App::getLocale();
              $q->where("name->$locale", 'like', "%{$search}%")
                ->orWhere("name->en", 'like', "%{$search}%");
            });
          }),


        Tables\Columns\TextColumn::make('quantity')->label('الكمية'),
        Tables\Columns\TextColumn::make('price')->label('السعر')->money('USD', locale: 'en_US'),
        Tables\Columns\TextColumn::make('full_price')->label('الإجمالي')
          ->money('USD', locale: 'en_US')
          ->summarize(
            Tables\Columns\Summarizers\Sum::make()
              ->label('المجموع الكلي')
              ->money('USD', locale: 'en_US')
          ),
        Tables\Columns\TextColumn::make('cashier.user.name')
          ->label('الكاشير')
          ->searchable(query: function (Builder $query, string $search): Builder {
            return $query->whereHas('cashier.user', function (Builder $q) use ($search) {
              $q->where('name', 'like', "%{$search}%");
            });
          }),

      ])
      ->defaultSort('created_at', 'DESC')
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

  public static function getEloquentQuery(): Builder
  {
    $query = parent::getEloquentQuery()->with(['variant.product', 'cashier.user', 'fatora']);
    if (auth()->user()->hasRole('super_admin')) {
      return $query;
    }

    $cashierId = SalesPointCashier::where('user_id', auth()->id())->value('id');

    return $query->where('sales_point_cashier_id', $cashierId);
  }

  public static function getNavigationItems(): array
  {
    return [
      parent::getNavigationItems()[0]->isActiveWhen(function () {
        return request()->routeIs('filament.admin.resources.cashier-sales.*')
          && !request()->routeIs('filament.admin.resources.cashier-sales.pages.pos');
      }),
    ];
  }

}
