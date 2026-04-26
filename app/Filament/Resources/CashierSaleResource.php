<?php

namespace App\Filament\Resources;

use App\Filament\Exports\CashierSaleExporter;
use App\Filament\Resources\CashierSaleResource\Pages;
use App\Models\CashierSale;
use App\Models\ProductVariant;
use App\Models\SalesPointCashier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Collection;

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
      Section::make('تفاصيل المادة المباعة')
        ->schema([
          Select::make('sales_point_cashier_id')
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

          Select::make('product_variant_id')
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
                ->with('product')
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

          TextInput::make('quantity')
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

          TextInput::make('price')
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

          TextInput::make('discount')
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
          TextInput::make('full_price')
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
      ->modifyQueryUsing(fn(Builder $query) => $query->with([
        'variant.product',
        'cashier.user',
        'fatora'
      ]))
      ->columns([
        TextColumn::make('fatora.id')->label('رقم الفاتورة')->sortable(),

        TextColumn::make('variant.product.name')
          ->label('المنتج')
          ->getStateUsing(fn($record) => $record->variant?->product?->name[App::getLocale()] ?? $record->variant?->product?->name['en'] ?? '')
          ->searchable(query: function (Builder $query, string $search): Builder {
            return $query->whereHas('variant.product', function (Builder $q) use ($search) {
              $locale = App::getLocale();
              $q->where("name->$locale", 'like', "%{$search}%")
                ->orWhere("name->en", 'like', "%{$search}%");
            });
          }),


        TextColumn::make('quantity')->label('الكمية'),
        TextColumn::make('price')->label('السعر')->money('USD', locale: 'en_US'),
        TextColumn::make('full_price')->label('الإجمالي')
          ->money('USD', locale: 'en_US')
          ->summarize(
            Sum::make()
              ->label('المجموع الكلي')
              ->money('USD', locale: 'en_US')
          ),
        TextColumn::make('cashier.user.name')
          ->label('الكاشير')
          ->searchable(query: function (Builder $query, string $search): Builder {
            return $query->whereHas('cashier.user', function (Builder $q) use ($search) {
              $q->where('name', 'like', "%{$search}%");
            });
          }),
      ])

      ->filters([
        TrashedFilter::make()
          ->label('حالة السجلات')
          ->falseLabel('السجلات المؤرشفة فقط')
          ->trueLabel('السجلات النشطة فقط')
          ->placeholder('الكل')
          ->native(false),
      ])
      ->defaultSort('created_at', 'DESC')
      ->actions([
        EditAction::make(),
        DeleteAction::make()
          ->label('أرشفة'),
        RestoreAction::make()
          ->label('استعادة'),
        ForceDeleteAction::make()
          ->label('حذف نهائي')
          ->before(function (ForceDeleteAction $action, $record) {
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
        BulkActionGroup::make([
          DeleteBulkAction::make()
            ->label('أرشفة المحدد'),
          RestoreBulkAction::make()
            ->label('استعادة المحدد'),
          ForceDeleteBulkAction::make()
            ->label('حذف نهائي للمحدد')
            ->before(function (ForceDeleteBulkAction $action, Collection $records) {
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
        ExportBulkAction::make()
          ->exporter(CashierSaleExporter::class)
          ->color('success')
          ->icon('heroicon-o-arrow-down-tray')
          ->formats([ExportFormat::Csv, ExportFormat::Xlsx])
          ->visible(fn() => auth()->user()->hasRole('super_admin')),
      ])
      ->headerActions([
        ExportAction::make()
          ->exporter(CashierSaleExporter::class)
          ->color('success')->icon('heroicon-o-arrow-down-tray')
          ->formats([ExportFormat::Csv, ExportFormat::Xlsx])
          ->visible(fn() => auth()->user()->hasRole('super_admin')),

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
      return $query->where('sales_point_cashier_id', $cashierId);
    }

    return $query->whereRaw('1 = 0');
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
