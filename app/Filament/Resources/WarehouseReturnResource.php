<?php

namespace App\Filament\Resources;

use App\Filament\Exports\WarehouseReturnExporter;
use App\Filament\Resources\WarehouseReturnResource\Pages;
use App\Models\ProductVariant;
use App\Models\ShippingWarehouse;
use App\Models\WarehouseReturn;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class WarehouseReturnResource extends Resource
{
  protected static ?string $model = WarehouseReturn::class;
  protected static ?string $navigationIcon = 'heroicon-o-arrow-uturn-left';
  protected static ?int $navigationSort = 5;
  protected static ?string $navigationLabel = 'المرتجعات من المستودعات';
  protected static ?string $pluralModelLabel = 'المرتجعات';
  protected static ?string $modelLabel = 'شحنة مرتجع';
  protected static ?string $navigationGroup = 'شحن و استيراد';


  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Section::make('تعديل بيانات الشحنة')
          ->visible(fn($context) => in_array($context, ['edit', 'view']))
          ->schema([
            ...static::getCommonFields(),
          ]),

        Repeater::make('shipping_items')
          ->label('إضافة شحنات متعددة')
          ->visible(fn($context) => $context === 'create')
          ->schema([
            ...static::getCommonFields(),
          ])
          ->addActionLabel('إضافة شحنة أخرى')
          ->collapsible()
          ->defaultItems(1)
          ->columnSpanFull(),
      ])->columns(1);
  }


  public static function getCommonFields(): array
  {
    return [
      Grid::make(3)
        ->schema([
          Select::make('warehouse_id')
            ->label('المستودع')
            ->relationship('warehouse', 'name')
            ->searchable()
            ->preload()
            ->live()
            ->required()

            ->afterStateUpdated(function (Set $set) {
              $set('product_variant_id', null);
              $set('amount', 0);
              $set('units_count', 0);
            }),

          Select::make('product_variant_id')
            ->label('المنتج (المتوفر في هذا المستودع)')
            ->required()
            ->live()
            ->searchable()
            ->preload()
            ->disabled(fn(Get $get) => !$get('warehouse_id'))
            ->relationship(
              name: 'productVariant',
              titleAttribute: 'sku',
              modifyQueryUsing: function (Builder $query, Get $get) {
                $warehouseId = $get('warehouse_id');

                if (!$warehouseId) {
                  return $query->whereRaw('1 = 0');
                }

                return $query->whereHas('shippingWarehouses', function ($q) use ($warehouseId) {
                  $q->where('warehouse_id', $warehouseId);
                })->with('product');
              }
            )

            ->afterStateUpdated(function (Set $set) {
              $set('amount', 0);
              $set('units_count', 0);
            })


            ->getOptionLabelFromRecordUsing(function ($record) {
              $productName = $record->product?->name['ar'] ?? 'منتج غير معروف';
              return "{$productName} - {$record->sku}";
            })
            ->getSearchResultsUsing(function (string $search, Get $get) {
              $warehouseId = $get('warehouse_id');
              if (!$warehouseId)
                return [];
              return ProductVariant::query()
                ->whereHas('shippingWarehouses', function ($q) use ($warehouseId) {
                  $q->where('warehouse_id', $warehouseId);
                })
                ->where(function ($q) use ($search) {
                  $q->where('sku', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($pq) use ($search) {
                      $pq->where('name->ar', 'like', "%{$search}%");
                    });
                })
                ->limit(50)
                ->get()
                ->mapWithKeys(function ($record) {
                  $productName = $record->product?->name['ar'] ?? 'منتج غير معروف';
                  return [$record->id => "{$productName} - {$record->sku}"];
                });
            }),

          Select::make('user_id')
            ->label('المستخدم المسؤول')
            ->relationship('user', 'name')
            ->default(auth()->id())
            ->disabled()
            ->dehydrated()
            ->required(),

          TextInput::make('unit_name')
            ->label('اسم الوحدة')
            ->placeholder('مثلاً: كرتونة')
            ->datalist(['كرتونة', 'طرد', 'صندوق']),

          TextInput::make('unit_capacity')
            ->label('سعة الوحدة')
            ->numeric()
            ->default(1)
            ->live()
            ->afterStateUpdated(function (Get $get, Set $set, $state) {
              $unitsCount = (int) $get('units_count') ?: 0;
              $set('amount', $unitsCount * (int) $state);
            }),

          TextInput::make('units_count')
            ->label('عدد الوحدات')
            ->numeric()
            ->live()
            ->dehydrated(false)
            ->afterStateHydrated(function (Set $set, Get $get, $record) {
              if ($record && $record->unit_capacity > 0) {
                $set('units_count', (int) ($record->amount / $record->unit_capacity));
              }
            })
            ->afterStateUpdated(function (Get $get, Set $set, $state) {
              $capacity = (int) $get('unit_capacity') ?: 1;
              $set('amount', (int) $state * $capacity);
            }),


          TextInput::make('amount')
            ->label('إجمالي الكمية (قطع)')
            ->numeric()
            ->required()
            ->live()
            ->readOnly()
            ->hint(function (Get $get) {
              $variantId = $get('product_variant_id');
              $warehouseId = $get('warehouse_id');

              if (!$variantId || !$warehouseId) {
                return null;
              }
              $subStock = ShippingWarehouse::where('product_variant_id', $variantId)
                ->where('warehouse_id', $warehouseId)
                ->sum('amount') ?? 0;
              return "المتوفر في هذا المستودع: {$subStock}";
            })
            ->hintColor('info')
            ->maxValue(function (Get $get) {
              $variantId = $get('product_variant_id');
              $warehouseId = $get('warehouse_id');

              if (!$variantId || !$warehouseId) {
                return 99999;
              }

              return ShippingWarehouse::where('product_variant_id', $variantId)
                ->where('warehouse_id', $warehouseId)
                ->sum('amount') ?? 0;
            })
            ->validationMessages([
              'max' => 'عذراً، الكمية المطلوبة غير متوفرة في هذا المستودع. المتاح هو :max فقط.',
            ]),

          DateTimePicker::make('arrival_time')
            ->label('وقت الوصول المتوقع')
            ->required(),
        ]),
    ];
  }


  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('warehouse.name')
          ->label('المستودع')
          ->searchable()
          ->sortable(),

        TextColumn::make('user.name')
          ->label('المستخدم')
          ->searchable()
          ->sortable(),

        TextColumn::make('productVariant.product.name')
          ->label('المنتج')
          ->getStateUsing(fn($record) => $record->productVariant->product->name['ar'] ?? '')
          ->description(fn($record) => "SKU: " . ($record->productVariant->sku ?? '-')),

        TextColumn::make('unit_info')
          ->label('تفاصيل التعبئة')
          ->getStateUsing(function ($record) {
            if (!$record->unit_name)
              return 'قطع منفصلة';
            return "{$record->unit_name} (سعة {$record->unit_capacity})";
          })
          ->icon('heroicon-m-cube')
          ->color('gray'),

        TextColumn::make('amount')
          ->label('الكمية الإجمالية')
          ->badge()
          ->color('success')
          ->suffix(' قطعة')
          ->sortable(),

        TextColumn::make('arrival_time')
          ->label('وقت الوصول')
          ->dateTime('Y-m-d H:i')
          ->sortable(),
      ])
      ->defaultSort('created_at', 'DESC')
      ->filters([
        SelectFilter::make('warehouse_id')
          ->label('تصفية حسب المستودع')
          ->relationship('warehouse', 'name'),
        TrashedFilter::make()
          ->label('حالة السجلات')
          ->falseLabel('السجلات المؤرشفة فقط')
          ->trueLabel('السجلات النشطة فقط')
          ->placeholder('الكل')
          ->native(false),
      ])
      ->actions([
        EditAction::make(),
        DeleteAction::make()
          ->label('أرشفة'),
        RestoreAction::make()
          ->label('استعادة'),
        ForceDeleteAction::make()
          ->label('حذف نهائي')
          ->before(function (ForceDeleteAction $action, $record) {
            if ($record->amount > 0) {
              Notification::make()
                ->title('فشل الحذف النهائي')
                ->body("لا يمكن حذف هذه الشحنة نهائياً لأن الكمية المسجلة بها ({$record->amount}) لم يتم تصفيرها أو استردادها.")
                ->danger()
                ->send();

              $action->halt();
            }
          }),
      ])
      ->bulkActions([
        BulkActionGroup::make([
          DeleteBulkAction::make()->label('أرشفة المحدد'),
          RestoreBulkAction::make()->label('استعادة المحدد'),

          ForceDeleteBulkAction::make()
            ->label('حذف نهائي للمحدد')
            ->before(function (ForceDeleteBulkAction $action, Collection $records) {
              $invalidRecords = $records->where('amount', '>', 0);

              if ($invalidRecords->count() > 0) {
                Notification::make()
                  ->title('إجراء غير مسموح')
                  ->body('بعض الشحنات المختارة لا تزال تحتوي على كميات. يجب تصفير الكميات قبل الحذف النهائي.')
                  ->danger()
                  ->send();

                $action->halt();
              }
            }),
        ]),
        ExportBulkAction::make()
          ->exporter(WarehouseReturnExporter::class)
          ->color('success')->icon('heroicon-o-arrow-down-tray')
          ->formats([ExportFormat::Csv, ExportFormat::Xlsx])
          ->visible(fn() => auth()->user()->hasRole('super_admin')),
      ])
      ->headerActions([
        ExportAction::make()
          ->exporter(WarehouseReturnExporter::class)
          ->color('success')
          ->icon('heroicon-o-arrow-down-tray')
          ->formats([ExportFormat::Csv, ExportFormat::Xlsx])
          ->visible(fn() => auth()->user()->hasRole('super_admin')),
      ]);
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListWarehouseReturns::route('/'),
      'create' => Pages\CreateWarehouseReturn::route('/create'),
      'edit' => Pages\EditWarehouseReturn::route('/{record}/edit'),
    ];
  }


  public static function getEloquentQuery(): Builder
  {
    $query = parent::getEloquentQuery()
      ->forActiveYear()
      ->with([
        'productVariant.product',
        'productVariant.images',
        'user',
        'warehouse'
      ]);
    $user = auth()->user();
    if ($user->hasRole('super_admin')) {
      return $query;
    }
    if ($user->hasRole('main_warehouse_manager')) {
      return $query;
    }
    if ($user->hasRole('sub_warehouse_manager')) {
      return $query->where('user_id', $user->id);
    }
    return $query->whereRaw('1 = 0');
  }
}
