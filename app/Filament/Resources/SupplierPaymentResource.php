<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierPaymentResource\Pages;
use App\Models\ProductImportItem;
use App\Models\SupplierPayment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Get;
use Filament\Forms\Set;

class SupplierPaymentResource extends Resource
{
  protected static ?string $model = SupplierPayment::class;
  protected static ?string $navigationIcon = 'heroicon-o-credit-card';
  protected static ?string $navigationGroup = 'شحن و استيراد';
  protected static ?string $navigationLabel = 'مدفوعات الموردين';
  protected static ?string $pluralModelLabel = 'مدفوعات الموردين';
  protected static ?string $modelLabel = 'مدفوعات الموردين';
  protected static ?int $navigationSort = 3;

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make('تفاصيل الدفعة المالية')
          ->schema([
            Forms\Components\Select::make('product_import_item_id')
              ->label('اختيار الفاتورة (المورد)')
              ->relationship('productImportItem', 'id')
              ->getOptionLabelFromRecordUsing(
                fn($record) => "المورد: {$record->productImport->supplier_name} | SKU: {$record->productVariant->sku}"
              )
              ->searchable()
              ->preload()
              ->required()
              ->live()
              ->afterStateUpdated(function ($state, Set $set) {
                if (!$state)
                  return;

                $importItem = ProductImportItem::find($state);
                if ($importItem) {
                  $set('amount', $importItem->remaining_amount);
                }
              })
              ->hint(function (Get $get) {
                $id = $get('product_import_item_id');
                if (!$id)
                  return null;

                $item = ProductImportItem::find($id);
                if (!$item)
                  return null;

                return "إجمالي الفاتورة: {$item->total_cost}$ | المتبقي بذمتك: {$item->remaining_amount}$";
              })
              ->hintColor('danger')
              ->hintIcon('heroicon-m-information-circle'),

            Forms\Components\TextInput::make('amount')
              ->label('المبلغ المراد دفعه الآن')
              ->numeric()
              ->prefix('$')
              ->required()
              ->live()
              ->maxValue(function (Get $get) {
                $id = $get('product_import_item_id');
                if (!$id)
                  return 1000000;

                $item = ProductImportItem::find($id);
                return $item ? $item->remaining_amount : 0;
              })
              ->helperText('لا يمكنك دفع مبلغ أكبر من المتبقي على الفاتورة.'),

            Forms\Components\DatePicker::make('payment_date')
              ->label('تاريخ الدفع')
              ->default(now())
              ->required(),

            Forms\Components\Select::make('trans_type')
              ->label('نوع العملية')
              ->options([
                'deposit' => 'دائن',
                'withdraw' => 'مدين',
              ])
              ->required(),

            Forms\Components\Select::make('payment_method')
              ->label('طريقة الدفع')
              ->options([
                'cash' => 'كاش',
                'bank_transfer' => 'تحويل بنكي',
              ])
              ->required(),

            Forms\Components\Textarea::make('notes')
              ->label('ملاحظات إضافية')
              ->columnSpanFull(),
          ])->columns(2),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('productImportItem.productImport.supplier_name')
          ->label('المورد')
          ->sortable()
          ->searchable(query: function (Builder $query, string $search): Builder {
            return $query->whereHas('productImportItem.productImport', function (Builder $q) use ($search) {
              $q->where('supplier_name', 'like', "%{$search}%");
            });
          }),

        TextColumn::make('amount')
          ->label('المبلغ')
          ->money('USD', locale: 'en_US')
          ->sortable()
          ->summarize(Tables\Columns\Summarizers\Sum::make()->label('الإجمالي')->money('USD', locale: 'en_US')),

        TextColumn::make('payment_method')
          ->label('الطريقة')
          ->badge()
          ->color('gray')
          ->formatStateUsing(fn(string $state): string => match ($state) {
            'cash' => 'كاش',
            'bank_transfer' => 'تحويل بنكي',
            default => $state,
          }),

        Tables\Columns\TextColumn::make('trans_type')
          ->label('نوع العملية')
          ->badge()
          ->formatStateUsing(fn(string $state): string => match ($state) {
            'deposit' => 'دائن',
            'withdraw' => 'مدين',
            default => $state,
          })
          ->color(fn(string $state): string => match ($state) {
            'deposit' => 'success',
            'withdraw' => 'danger',
            default => 'gray',
          })
          ->icon(fn(string $state): string => match ($state) {
            'deposit' => 'heroicon-m-arrow-trending-up',
            'withdraw' => 'heroicon-m-arrow-trending-down',
            default => 'heroicon-m-minus',
          }),
        TextColumn::make('payment_date')
          ->label('تاريخ الدفع')
          ->date('Y-m-d')
          ->sortable(),


      ])
      ->filters([

        Tables\Filters\SelectFilter::make('trans_type')
          ->label('نوع الحركة')
          ->options([
            'deposit' => 'إيداع',
            'withdraw' => 'سحب',
          ]),


        SelectFilter::make('payment_method')
          ->label('طريقة الدفع')
          ->options([
            'cash' => 'كاش',
            'bank_transfer' => 'تحويل بنكي',
          ]),

        Tables\Filters\Filter::make('payment_date')
          ->form([
            Forms\Components\DatePicker::make('from')->label('من تاريخ'),
            Forms\Components\DatePicker::make('until')->label('إلى تاريخ'),
          ])
          ->query(function (Builder $query, array $data): Builder {
            return $query
              ->when($data['from'], fn($q) => $q->whereDate('payment_date', '>=', $data['from']))
              ->when($data['until'], fn($q) => $q->whereDate('payment_date', '<=', $data['until']));
          })
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
      'index' => Pages\ListSupplierPayments::route('/'),
      'create' => Pages\CreateSupplierPayment::route('/create'),
      'edit' => Pages\EditSupplierPayment::route('/{record}/edit'),
    ];
  }
}
