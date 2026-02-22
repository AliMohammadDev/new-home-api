<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductImportItemResource\Pages;
use App\Models\ProductImportItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductImportItemResource extends Resource
{
  protected static ?string $model = ProductImportItem::class;
  protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
  protected static ?string $navigationLabel = 'عملية استيراد بضاعة';
  protected static ?string $pluralModelLabel = 'عملية استيراد بضاعة';
  protected static ?string $modelLabel = ' عملية ';
  protected static ?string $navigationGroup = 'شحن و استيراد';

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make('تفاصيل الصنف الوارد')
          ->schema([
            Forms\Components\Select::make('product_import_id')
              ->label('الفاتورة / المورد')
              ->relationship('productImport', 'supplier_name')
              ->searchable()
              ->preload()
              ->required(),

            Forms\Components\Select::make('product_variant_id')
              ->label('المنتج (الخيار)')
              ->relationship('productVariant', 'sku')
              ->getOptionLabelFromRecordUsing(fn($record) => "{$record->product->name['ar']} - {$record->sku}")
              ->searchable()
              ->preload()
              ->required(),

            Forms\Components\TextInput::make('quantity')
              ->label('الكمية')
              ->numeric()
              ->default(1)
              ->required()
              ->live()
              ->afterStateUpdated(fn($set, $get) => self::updateTotal($set, $get)),

            Forms\Components\TextInput::make('price')
              ->label('سعر الوحدة')
              ->numeric()
              ->prefix('$')
              ->required()
              ->live()
              ->afterStateUpdated(fn($set, $get) => self::updateTotal($set, $get)),

            Forms\Components\TextInput::make('shipping_price')
              ->label('تكلفة الشحن للوحدة')
              ->numeric()
              ->prefix('$')
              ->default(0)
              ->live()
              ->afterStateUpdated(fn($set, $get) => self::updateTotal($set, $get)),

            Forms\Components\TextInput::make('discount')
              ->label('خصم إجمالي')
              ->numeric()
              ->prefix('$')
              ->default(0)
              ->live()
              ->afterStateUpdated(fn($set, $get) => self::updateTotal($set, $get)),

            Forms\Components\TextInput::make('total_cost')
              ->label('إجمالي التكلفة النهائية')
              ->prefix('$')
              ->numeric()
              ->readOnly()
              ->dehydrated()
              ->extraInputAttributes([
                'class' => 'bg-gray-100 dark:bg-gray-800 font-bold text-primary-600',
              ]),

            Forms\Components\DateTimePicker::make('expected_arrival')
              ->label('موعد الوصول المتوقع')
              ->displayFormat('Y-m-d H:i'),
          ])->columns(2),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('productImport.supplier_name')
          ->label('المورد')->sortable()->searchable(),

        Tables\Columns\TextColumn::make('productVariant.product.name')
          ->label('المنتج')
          ->getStateUsing(function ($record) {
            $product = $record->productVariant?->product;
            if (!$product)
              return '-';

            return $product->name[app()->getLocale()] ?? $product->name['ar'] ?? $product->name['en'] ?? '-';
          })
          ->searchable(),



        Tables\Columns\TextColumn::make('quantity')
          ->label('الكمية')->badge()->color('success'),

        Tables\Columns\TextColumn::make('price')
          ->label('السعر')->money('USD', locale: 'en_US')->color('success'),

        Tables\Columns\TextColumn::make('discount')
          ->label('الخصم')->money('USD', locale: 'en_US', )->color('danger'),

        Tables\Columns\TextColumn::make('expected_arrival')
          ->label('تاريخ الوصول')->dateTime('Y-m-d H:i'),
      ])
      ->filters([
        Tables\Filters\SelectFilter::make('product_import_id')
          ->label('المورد')
          ->relationship('productImport', 'supplier_name'),
      ])
      ->actions([
        Tables\Actions\Action::make('print')
          ->label('طباعة')
          ->icon('heroicon-o-printer')
          ->color('info')
          ->url(fn($record) => route('supplier.print', ['ids' => [$record->id]]))
          ->openUrlInNewTab(),

        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\BulkAction::make('print_selected')
            ->label('طباعة المحدد (PDF)')
            ->icon('heroicon-o-printer')
            ->color('success')
            ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
              return redirect()->route('supplier.print', [
                'ids' => $records->pluck('id')->toArray()
              ]);
            }),

          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ]);
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListProductImportItems::route('/'),
      'create' => Pages\CreateProductImportItem::route('/create'),
      'edit' => Pages\EditProductImportItem::route('/{record}/edit'),
    ];
  }

  protected static function updateTotal($set, $get)
  {
    $price = (float) ($get('price') ?? 0);
    $shipping = (float) ($get('shipping_price') ?? 0);
    $quantity = (float) ($get('quantity') ?? 0);
    $discount = (float) ($get('discount') ?? 0);

    $total = (($price + $shipping) * $quantity) - $discount;

    $set('total_cost', number_format(max(0, $total), 2, '.', ''));
  }
}
