<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashierSalesReturnResource\Pages;
use App\Models\CashierSalesReturn;
use App\Models\ProductVariant;
use App\Models\SalesPointCashier;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;

class CashierSalesReturnResource extends Resource
{
  protected static ?string $model = CashierSalesReturn::class;
  protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
  protected static ?string $navigationGroup = 'إدارة المبيعات';
  protected static ?int $navigationSort = 6;
  protected static ?string $navigationLabel = 'مرتجعات الكاشير (الأصناف) ';
  protected static ?string $pluralModelLabel = 'أصناف مرتجعة';
  protected static ?string $modelLabel = 'مرتجع صنف';

  public static function form(Form $form): Form
  {
    return $form->schema([
      Forms\Components\Section::make('تفاصيل المادة المرتجعة')
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
            ->disabled()
            ->dehydrated()
            ->required()
            ->prefix('USD'),

          Forms\Components\TextInput::make('full_price')
            ->label('الإجمالي المرتجع')
            ->numeric()
            ->readonly()
            ->prefix('USD'),
        ])->columns(2),
    ]);
  }

  public static function table(Table $table): Table
  {
    return $table->columns([
      Tables\Columns\TextColumn::make('fatora.id')
        ->label('رقم فاتورة المرتجع')
        ->sortable()
        ->searchable(query: function (Builder $query, string $search): Builder {
          return $query->whereHas('fatora', function (Builder $q) use ($search) {
            $q->where('id', 'like', "%{$search}%");
          });
        }),
      Tables\Columns\TextColumn::make('variant.product.name')
        ->label('المنتج')
        ->getStateUsing(
          fn($record) =>
          $record->variant?->product?->name[App::getLocale()] ??
          $record->variant?->product?->name['en'] ?? ''
        )
        ->searchable(query: function (Builder $query, string $search): Builder {
          return $query->whereHas('variant.product', function (Builder $q) use ($search) {
            $locale = App::getLocale();
            $q->where("name->$locale", 'like', "%{$search}%")
              ->orWhere("name->en", 'like', "%{$search}%");
          });
        }),
      Tables\Columns\TextColumn::make('quantity')->label('الكمية')->color('danger'),
      Tables\Columns\TextColumn::make('full_price')
        ->label('الإجمالي')
        ->money('USD', locale: 'en_US')

        ->summarize(
          Tables\Columns\Summarizers\Sum::make()
            ->label('إجمالي المرتجعات')
            ->money('USD', locale: 'en_US')
        ),

      Tables\Columns\TextColumn::make('cashier.user.name')
        ->label('بواسطة الكاشير')
        ->searchable(query: function (Builder $query, string $search): Builder {
          return $query->whereHas('cashier.user', function (Builder $q) use ($search) {
            $q->where('name', 'like', "%{$search}%");
          });
        }),
    ])
      ->filters([
        Tables\Filters\TrashedFilter::make()
          ->label('حالة السجلات')
          ->falseLabel('السجلات المؤرشفة فقط')
          ->trueLabel('السجلات النشطة فقط')
          ->placeholder('الكل')
          ->native(false),
      ])

      ->defaultSort('created_at', 'DESC')
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make()
          ->label('أرشفة'),
        Tables\Actions\RestoreAction::make()
          ->label('استعادة'),
        Tables\Actions\ForceDeleteAction::make()
          ->label('حذف نهائي')
          ->before(function (Tables\Actions\ForceDeleteAction $action, $record) {
            if ($record->quantity != 0) {
              Notification::make()
                ->title('غير مسموح')
                ->body('يجب تصفير المبلغ أولاً قبل الحذف النهائي.')
                ->warning()
                ->send();
              $action->halt();
            }
          }),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make()
            ->label('أرشفة المحدد'),
          Tables\Actions\RestoreBulkAction::make()
            ->label('استعادة المحدد'),
          Tables\Actions\ForceDeleteBulkAction::make()
            ->label('حذف نهائي للمحدد')
            ->before(function (Tables\Actions\ForceDeleteBulkAction $action, \Illuminate\Database\Eloquent\Collection $records) {
              $invalidRecords = $records->where('quantity', '!=', 0);

              if ($invalidRecords->count() > 0) {
                Notification::make()
                  ->title('لا يمكن الحذف النهائي')
                  ->body('بعض السجلات المختارة تحتوي على مبالغ غير صفرية. يجب تصفير المبالغ أولاً.')
                  ->danger()
                  ->send();

                $action->halt();
              }
            }),
        ]),
      ]);
  }
  public static function getRelations(): array
  {
    return [
      //
    ];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListCashierSalesReturns::route('/'),
      'create' => Pages\CreateCashierSalesReturn::route('/create'),
      'edit' => Pages\EditCashierSalesReturn::route('/{record}/edit'),
    ];
  }

  public static function getEloquentQuery(): Builder
  {
    $query = parent::getEloquentQuery()
      ->withTrashed()
      ->with(['variant.product', 'cashier.user', 'fatora']);
    $user = auth()->user();

    if ($user->hasRole('super_admin')) {
      return $query;
    }

    if ($user->hasRole('sales_point_manager')) {
      return $query->whereHas('cashier.salesPoint.managers', function (Builder $subQuery) use ($user) {
        $subQuery->where('user_id', $user->id);
      });
    }

    if ($user->hasRole('sales_point_cashier')) {
      $cashierId = SalesPointCashier::where('user_id', $user->id)->value('id');

      if (!$cashierId) {
        return $query->whereRaw('1 = 0');
      }

      return $query->where('sales_point_cashier_id', $cashierId);
    }

    return $query->whereRaw('1 = 0');
  }
}
