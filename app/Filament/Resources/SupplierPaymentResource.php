<?php

namespace App\Filament\Resources;

use App\Filament\Exports\SupplierPaymentExporter;
use App\Filament\Resources\SupplierPaymentResource\Pages;
use App\Models\ProductImportItem;
use App\Models\SupplierPayment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Columns\Summarizers\Summarizer;

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
        Section::make('تفاصيل الدفعة المالية')
          ->schema([
            Select::make('product_import_item_id')
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
                  $set('remaining_balance_display', 0);
                }
              }),

            TextInput::make('amount')
              ->label('المبلغ المراد دفعه الآن')
              ->numeric()
              ->prefix('$')
              ->required()
              ->live(onBlur: false)
              ->maxValue(function (Get $get, $record) {
                $id = $get('product_import_item_id');
                if (!$id)
                  return 1000000;
                $item = ProductImportItem::find($id);
                if (!$item)
                  return 0;
                $currentPaymentAmount = $record ? $record->amount : 0;
                return (float) $item->remaining_amount + (float) $currentPaymentAmount;
              })
              ->afterStateUpdated(function ($state, Get $get, Set $set, $record) {
                $id = $get('product_import_item_id');
                if (!$id)
                  return;
                $item = ProductImportItem::find($id);
                if (!$item)
                  return;

                $originalRemaining = (float) $item->remaining_amount + ($record ? (float) $record->amount : 0);
                $newRemaining = $originalRemaining - (float) $state;

                $set('remaining_balance_display', number_format($newRemaining, 2, '.', ''));
              }),

            TextInput::make('remaining_balance_display')
              ->label('الرصيد المتبقي بعد هذه العملية')
              ->prefix('$')
              ->readOnly()
              ->dehydrated(false)
              ->extraInputAttributes([
                'class' => 'text-danger-600 font-bold bg-gray-50 dark:bg-gray-800',
                'style' => 'color: #dc2626;'
              ])
              ->placeholder(function (Get $get, $record) {
                $id = $get('product_import_item_id');
                if (!$id)
                  return '0.00';

                $item = ProductImportItem::find($id);
                if (!$item)
                  return '0.00';

                $currentAmountInInput = (float) $get('amount');
                $originalRemaining = (float) $item->remaining_amount + ($record ? (float) $record->amount : 0);
                return number_format($originalRemaining - $currentAmountInInput, 2, '.', '');
              }),
            DatePicker::make('payment_date')
              ->label('تاريخ الدفع')
              ->default(now())
              ->required(),
            Select::make('trans_type')
              ->label('نوع العملية')
              ->options([
                'deposit' => 'دائن',
                'withdraw' => 'مدين',
              ])
              ->required(),

            Select::make('payment_method')
              ->label('طريقة الدفع')
              ->options([
                'cash' => 'كاش',
                'bank_transfer' => 'تحويل بنكي',
              ])
              ->required(),

            Textarea::make('notes')
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


        TextColumn::make('debit')
          ->label('مدين')
          ->getStateUsing(
            fn($record) =>
            $record->trans_type === 'withdraw' ? $record->amount : null
          )
          ->money('USD', locale: 'en_US')
          ->summarize(
            Summarizer::make()
              ->label('إجمالي المدين')
              ->using(function ($query) {
                return $query->where('trans_type', 'withdraw')->sum('amount');
              })
          ),

        TextColumn::make('credit')
          ->label('دائن')
          ->getStateUsing(
            fn($record) =>
            $record->trans_type === 'deposit' ? $record->amount : null
          )
          ->money('USD', locale: 'en_US')
          ->summarize(
            Summarizer::make()
              ->label('إجمالي الدائن')
              ->using(function ($query) {
                return $query->where('trans_type', 'deposit')->sum('amount');
              })
          ),

        TextColumn::make('payment_method')
          ->label('الطريقة')
          ->badge()
          ->color('gray')
          ->formatStateUsing(fn(string $state): string => match ($state) {
            'cash' => 'كاش',
            'bank_transfer' => 'تحويل بنكي',
            default => $state,
          }),


        TextColumn::make('notes')
          ->label('الطريقةالبيان'),


        // TextColumn::make('trans_type')
        //   ->label('نوع العملية')
        //   ->badge()
        //   ->formatStateUsing(fn(string $state): string => match ($state) {
        //     'deposit' => 'دائن',
        //     'withdraw' => 'مدين',
        //     default => $state,
        //   })
        //   ->color(fn(string $state): string => match ($state) {
        //     'deposit' => 'success',
        //     'withdraw' => 'danger',
        //     default => 'gray',
        //   })
        //   ->icon(fn(string $state): string => match ($state) {
        //     'deposit' => 'heroicon-m-arrow-trending-up',
        //     'withdraw' => 'heroicon-m-arrow-trending-down',
        //     default => 'heroicon-m-minus',
        //   }),

        // TextColumn::make('amount')
        //   ->label('المبلغ المدفوع')
        //   ->money('USD', locale: 'en_US')
        //   ->sortable()
        //   ->summarize(Sum::make()->label('الإجمالي')->money('USD', locale: 'en_US')),



        TextColumn::make('payment_date')
          ->label('تاريخ الدفع')
          ->date('Y-m-d')
          ->sortable(),
      ])
      ->filters([
        SelectFilter::make('trans_type')
          ->label('نوع الحركة')
          ->options([
            'deposit' => 'دائن',
            'withdraw' => 'مدين',
          ]),
        SelectFilter::make('payment_method')
          ->label('طريقة الدفع')
          ->options([
            'cash' => 'كاش',
            'bank_transfer' => 'تحويل بنكي',
          ]),

        Tables\Filters\Filter::make('payment_date')
          ->form([
            DatePicker::make('from')->label('من تاريخ'),
            DatePicker::make('until')->label('إلى تاريخ'),
          ])
          ->query(function (Builder $query, array $data): Builder {
            return $query
              ->when($data['from'], fn($q) => $q->whereDate('payment_date', '>=', $data['from']))
              ->when($data['until'], fn($q) => $q->whereDate('payment_date', '<=', $data['until']));
          })
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
            if ($record->amount != 0) {
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
          DeleteBulkAction::make()->label('أرشفة المحدد'),
          RestoreBulkAction::make()->label('استعادة المحدد'),

          ForceDeleteBulkAction::make()
            ->label('حذف نهائي للمحدد')
            ->before(function (ForceDeleteBulkAction $action, Collection $records) {
              $invalidRecords = $records->where('amount', '!=', 0);

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
        ExportBulkAction::make()->exporter(SupplierPaymentExporter::class)
          ->color('success')
          ->icon('heroicon-o-arrow-down-tray')
          ->formats([ExportFormat::Csv, ExportFormat::Xlsx])
          ->visible(fn() => auth()->user()->hasRole('super_admin')),
      ])
      ->headerActions([
        ExportAction::make()->exporter(SupplierPaymentExporter::class)
          ->color('success')
          ->icon('heroicon-o-arrow-down-tray')
          ->formats([ExportFormat::Csv, ExportFormat::Xlsx])
          ->visible(fn() => auth()->user()->hasRole('super_admin')),
      ]);
  }

  public static function getEloquentQuery(): Builder
  {
    return parent::getEloquentQuery()
      ->forActiveYear()
      ->with(['productImportItem.productImport', 'productImportItem.productVariant']);
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
