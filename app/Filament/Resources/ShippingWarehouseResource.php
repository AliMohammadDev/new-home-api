<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShippingWarehouseResource\Pages;
use App\Filament\Resources\ShippingWarehouseResource\RelationManagers;
use App\Models\ProductVariant;
use App\Models\ShippingWarehouse;
use App\Models\WarehouseReturn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShippingWarehouseResource extends Resource
{
  protected static ?string $model = ShippingWarehouse::class;

  protected static ?string $navigationIcon = 'heroicon-o-truck';
  protected static ?string $navigationLabel = ' شحنة مستودع مصغر';
  protected static ?string $pluralModelLabel = 'مخزون المستودعات';
  protected static ?string $modelLabel = 'شحنة مستودع';
  protected static ?string $navigationGroup = 'شحن و استيراد';

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make('تعديل بيانات الشحنة')
          ->visible(fn($context) => in_array($context, ['edit', 'view']))
          ->schema([
            ...static::getCommonFields(),
          ]),

        Forms\Components\Repeater::make('shipping_items')
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
      Forms\Components\Grid::make(3)
        ->schema([
          Forms\Components\Select::make('warehouse_id')
            ->label('المستودع')
            ->relationship('warehouse', 'name')
            ->searchable()
            ->preload()
            ->required(),

          Forms\Components\Select::make('product_variant_id')
            ->label('المنتج (النوع)')
            ->relationship(
              name: 'productVariant',
              titleAttribute: 'id',
              modifyQueryUsing: fn($query) => $query->with('product')
            )
            ->getOptionLabelFromRecordUsing(function ($record) {
              $productName = $record->product?->name['ar'] ?? 'منتج غير معروف';
              return "{$productName} - {$record->sku} (المتوفر: {$record->stock_quantity})";
            })
            ->live()
            ->required(),

          Forms\Components\TextInput::make('unit_name')
            ->label('اسم الوحدة')
            ->placeholder('مثلاً: كرتونة')
            ->datalist(['كرتونة', 'طرد', 'صندوق']),

          Forms\Components\TextInput::make('unit_capacity')
            ->label('سعة الوحدة')
            ->numeric()
            ->default(1)
            ->live()
            ->afterStateUpdated(function (Get $get, Set $set, $state) {
              $unitsCount = (int) $get('units_count') ?: 0;
              $set('amount', $unitsCount * (int) $state);
            }),

          Forms\Components\TextInput::make('units_count')
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

          Forms\Components\TextInput::make('amount')
            ->label('إجمالي الكمية (قطع)')
            ->numeric()
            ->required()
            ->helperText('يتم حسابه تلقائياً')
            ->maxValue(fn(Get $get) => ProductVariant::find($get('product_variant_id'))?->stock_quantity ?? 99999),

          Forms\Components\DateTimePicker::make('arrival_time')
            ->label('وقت الوصول المتوقع')
            ->required(),
        ]),
    ];
  }


  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('warehouse.name')
          ->label('المستودع')
          ->searchable()
          ->sortable(),

        Tables\Columns\TextColumn::make('productVariant.product.name')
          ->label('المنتج')
          ->getStateUsing(fn($record) => $record->productVariant->product->name['ar'] ?? '')
          ->description(fn($record) => "SKU: " . ($record->productVariant->sku ?? '-')),

        Tables\Columns\TextColumn::make('unit_info')
          ->label('تفاصيل التعبئة')
          ->getStateUsing(function ($record) {
            if (!$record->unit_name)
              return 'قطع منفصلة';
            return "{$record->unit_name} (سعة {$record->unit_capacity})";
          })
          ->icon('heroicon-m-cube')
          ->color('gray'),

        Tables\Columns\TextColumn::make('amount')
          ->label('الكمية الإجمالية')
          ->badge()
          ->color('success')
          ->suffix(' قطعة')
          ->sortable(),

        Tables\Columns\TextColumn::make('arrival_time')
          ->label('وقت الوصول')
          ->dateTime('Y-m-d H:i')
          ->sortable(),
      ])
      ->filters([
        Tables\Filters\SelectFilter::make('warehouse_id')
          ->label('تصفية حسب المستودع')
          ->relationship('warehouse', 'name'),
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make(),

        Tables\Actions\Action::make('return_items')
          ->label('استرجاع مرتجع')
          ->icon('heroicon-o-arrow-uturn-left')
          ->color('warning')
          ->modalHeading('تسجيل مرتجع من المستودع')
          ->modalSubmitActionLabel('إتمام الإرجاع')
          ->form([
            Forms\Components\TextInput::make('return_amount')
              ->label('الكمية المرتجعة')
              ->numeric()
              ->required()
              ->maxValue(fn($record) => $record->amount)
              ->hint(fn($record) => "الكمية المتاحة حالياً: {$record->amount}"),

            Forms\Components\Textarea::make('reason')
              ->label('سبب الإرجاع')
              ->required(),
          ])
          ->action(function (ShippingWarehouse $record, array $data): void {
            WarehouseReturn::create([
              'product_variant_id' => $record->product_variant_id,
              'warehouse_id' => $record->warehouse_id,
              'amount' => $data['return_amount'],
              'reason' => $data['reason'],
            ]);

            if ($record->amount <= $data['return_amount']) {
              $record->delete();
            } else {
              $record->decrement('amount', $data['return_amount']);
            }
          })
          ->requiresConfirmation()
          ->successNotificationTitle('تم تسجيل المرتجع وتحديث المخزون بنجاح')


      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
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
      'index' => Pages\ListShippingWarehouses::route('/'),
      'create' => Pages\CreateShippingWarehouse::route('/create'),
      'edit' => Pages\EditShippingWarehouse::route('/{record}/edit'),
    ];
  }
}
