<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShippingWarehouseResource\Pages;
use App\Filament\Resources\ShippingWarehouseResource\RelationManagers;
use App\Models\ProductVariant;
use App\Models\ShippingWarehouse;
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

  protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
  protected static ?string $navigationLabel = ' شحنة مستودع مصغر';
  protected static ?string $pluralModelLabel = 'مخزون المستودعات';
  protected static ?string $modelLabel = 'شحنة مستودع';
  protected static ?string $navigationGroup = 'شحن و استيراد';

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make('تفاصيل الشحن والمخزون')
          ->description('إدارة توزيع كميات المنتجات على المستودعات المصغرة')
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
              ->afterStateUpdated(function (Set $set, $state) {
                $set('amount', 0);
              })
              ->searchable()
              ->preload()
              ->required(),

            Forms\Components\TextInput::make('amount')
              ->label('الكمية التي سيتم شحنها')
              ->numeric()
              ->required()
              ->prefix('QTY')
              ->live(onBlur: true)
              ->hint(function (Get $get) {
                $variantId = $get('product_variant_id');
                if ($variantId) {
                  $stock = ProductVariant::find($variantId)?->stock_quantity ?? 0;
                  return "الحد الأقصى المتاح: " . $stock;
                }
                return null;
              })
              ->maxValue(function (Get $get) {
                $variantId = $get('product_variant_id');
                return $variantId ? ProductVariant::find($variantId)?->stock_quantity : 0;
              })
              ->validationMessages([
                'max' => 'الكمية المطلوبة أكبر من المتوفر في المخزن الرئيسي!',
              ]),

            Forms\Components\TextInput::make('arrival_time')
              ->label('وقت الوصول المتوقع')
              ->placeholder('مثلاً: 3-5 أيام')
              ->required(),
          ])->columns(2),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('warehouse.name')
          ->label('المستودع')
          ->sortable(),

        Tables\Columns\TextColumn::make('productVariant.product.name')
          ->label('المنتج')
          ->getStateUsing(fn($record) => $record->productVariant->product->name['ar'] ?? '')
          ->sortable(),

        Tables\Columns\TextColumn::make('amount')
          ->label('الكمية')
          ->badge()
          ->color(fn(int $state): string => $state < 10 ? 'danger' : 'success')
          ->sortable(),

        Tables\Columns\TextColumn::make('arrival_time')
          ->label('وقت الوصول')
          ->icon('heroicon-m-clock'),
      ])
      ->filters([
        Tables\Filters\SelectFilter::make('warehouse_id')
          ->label('فلترة حسب المستودع')
          ->relationship('warehouse', 'name'),
      ])
      ->actions([
        Tables\Actions\Action::make('print_single')
          ->label('طباعة')
          ->icon('heroicon-s-printer')
          ->color('gray')
          ->url(fn(ShippingWarehouse $record) =>
            route('shipping.print', ['ids' => [$record->id]]))
          ->openUrlInNewTab(),

        Tables\Actions\Action::make('return_to_warehouse')
          ->label('إرجاع')
          ->icon('heroicon-o-arrow-uturn-left')
          ->color('warning')
          ->requiresConfirmation()
          ->modalHeading('إرجاع الشحنة للمخزن الرئيسي')
          ->modalDescription('سيتم حذف هذه الشحنة وإعادة الكمية للمخزن الرئيسي. هل أنت متأكد؟')
          ->modalSubmitActionLabel('تأكيد الإرجاع')
          ->form([
            Forms\Components\Textarea::make('reason')
              ->label('سبب الإرجاع')
              ->required()
              ->placeholder('اكتب سبب الإرجاع هنا...'),
          ])
          ->action(function (ShippingWarehouse $record, array $data): void {
            \Illuminate\Support\Facades\DB::transaction(function () use ($record, $data) {

              \App\Models\WarehouseReturn::create([
                'warehouse_id' => $record->warehouse_id,
                'product_variant_id' => $record->product_variant_id,
                'amount' => $record->amount,
                'reason' => $data['reason'],
                'arrival_time' => 'Returned',
              ]);

              $record->delete();
            });

            \Filament\Notifications\Notification::make()
              ->title('تمت عملية الإرجاع بنجاح')
              ->body('تم تحديث المخزن الرئيسي عبر النظام التلقائي.')
              ->success()
              ->send();
          }),

        Tables\Actions\EditAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([

          Tables\Actions\DeleteBulkAction::make(),

          Tables\Actions\BulkAction::make('print_pdf')
            ->label('طباعة PDF للمحدد')
            ->icon('heroicon-m-printer')
            ->color('success')
            ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
              return redirect()->route('shipping.print', [
                'ids' => $records->pluck('id')->toArray()
              ]);
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
      'index' => Pages\ListShippingWarehouses::route('/'),
      'create' => Pages\CreateShippingWarehouse::route('/create'),
      'edit' => Pages\EditShippingWarehouse::route('/{record}/edit'),
    ];
  }
}