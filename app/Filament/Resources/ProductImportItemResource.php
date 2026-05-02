<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductImportItemResource\Pages;
use App\Models\ProductImportItem;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Collection;

class ProductImportItemResource extends Resource
{
  protected static ?string $model = ProductImportItem::class;
  protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
  protected static ?int $navigationSort = 2;
  protected static ?string $navigationLabel = 'عملية استيراد بضاعة';
  protected static ?string $pluralModelLabel = 'عملية استيراد بضاعة';
  protected static ?string $modelLabel = ' عملية ';
  protected static ?string $navigationGroup = 'شحن و استيراد';

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Section::make('تفاصيل الصنف الوارد')
          ->schema([
            Select::make('product_import_id')
              ->label('  المورد')
              ->relationship('productImport', 'supplier_name')
              ->searchable()
              ->preload()
              ->required(),

            Select::make('product_variant_id')
              ->label('المنتج (الخيار)')
              ->relationship('productVariant', 'sku')
              ->getOptionLabelFromRecordUsing(fn($record) => "{$record->product->name['ar']} - {$record->sku}")
              ->searchable()
              ->preload()
              ->required(),

            Select::make('user_id')
              ->label('المستخدم المسؤول')
              ->relationship('user', 'name')
              ->default(auth()->id())
              ->disabled()
              ->dehydrated()
              ->required(),


            TextInput::make('quantity')
              ->label('الكمية')
              ->numeric()
              ->default(1)
              ->required()
              ->live()
              ->afterStateUpdated(fn($set, $get) => self::updateTotal($set, $get)),

            TextInput::make('price')
              ->label('سعر الوحدة')
              ->numeric()
              ->prefix('$')
              ->required()
              ->live()
              ->afterStateUpdated(fn($set, $get) => self::updateTotal($set, $get)),

            TextInput::make('shipping_price')
              ->label('تكلفة الشحن للوحدة')
              ->numeric()
              ->prefix('$')
              ->default(0)
              ->live()
              ->afterStateUpdated(fn($set, $get) => self::updateTotal($set, $get)),

            TextInput::make('discount')
              ->label('خصم إجمالي')
              ->numeric()
              ->prefix('$')
              ->default(0)
              ->live()
              ->afterStateUpdated(fn($set, $get) => self::updateTotal($set, $get)),

            TextInput::make('total_cost')
              ->label('إجمالي التكلفة النهائية')
              ->prefix('$')
              ->numeric()
              ->readOnly()
              ->dehydrated()
              ->extraInputAttributes([
                'class' => 'bg-gray-100 dark:bg-gray-800 font-bold text-primary-600',
              ]),

            DateTimePicker::make('expected_arrival')
              ->label('موعد الوصول المتوقع')
              ->required()
              ->displayFormat('Y-m-d H:i'),
          ])->columns(2),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('productImport.supplier_name')
          ->label('المورد')->sortable()->searchable(),

        TextColumn::make('productVariant.product.name')
          ->label('المنتج')
          ->getStateUsing(function ($record) {
            $product = $record->productVariant?->product;
            if (!$product)
              return '-';

            return $product->name[app()->getLocale()] ?? $product->name['ar'] ?? $product->name['en'] ?? '-';
          })
          ->searchable(),
        TextColumn::make('user.name')
          ->label('المستخدم')
          ->sortable()
          ->searchable(),

        TextColumn::make('quantity')
          ->label('الكمية')->badge()->color('success'),

        TextColumn::make('price')
          ->label('السعر')->money('USD', locale: 'en_US')
          ->color('success'),
        TextColumn::make('shipping_price')
          ->label('شحن/وحدة')
          ->money('USD', locale: 'en_US')
          ->color('warning')
          ->alignCenter(),

        TextColumn::make('discount')
          ->label('الخصم')
          ->money('USD', locale: 'en_US')
          ->color('danger')
          ->default(0)
          ->alignCenter(),

        TextColumn::make('total_cost')
          ->label('الإجمالي النهائي')
          ->money('USD', locale: 'en_US')
          ->weight('bold')
          ->color('success')
          ->summarize(
            Sum::make()
              ->label('الإجمالي النهائي')
              ->money('USD', locale: 'en_US')
          ),

        TextColumn::make('expected_arrival')
          ->label('تاريخ الوصول')->dateTime('Y-m-d H:i'),
      ])
      ->filters([
        SelectFilter::make('product_import_id')
          ->label('المورد')
          ->relationship('productImport', 'supplier_name'),


        TrashedFilter::make()
          ->label('حالة السجلات')
          ->falseLabel('السجلات المؤرشفة فقط')
          ->trueLabel('السجلات النشطة فقط')
          ->placeholder('الكل')
          ->native(false),

      ])
      ->defaultSort('created_at', 'DESC')
      ->actions([
        EditAction::make()
          ->color(fn($record) => $record->payments()->exists() ? 'gray' : 'primary')
          ->icon(fn($record) => $record->payments()->exists() ? 'heroicon-m-lock-closed' : 'heroicon-m-pencil-square')
          ->before(function (EditAction $action, $record) {
            if ($record->payments()->exists()) {
              Notification::make()
                ->title('السجل مقفل ماليًا')
                ->body('لا يمكن التعديل بسبب وجود دفعات مسجلة.')
                ->warning()
                ->send();
              $action->halt();
            }
          }),

        Action::make('print')
          ->label('طباعة')
          ->icon('heroicon-o-printer')
          ->color('info')
          ->visible(fn($record) => !$record->trashed())
          ->url(fn($record) => route('product.import.print', ['ids' => [$record->id]]))
          ->openUrlInNewTab(),


        DeleteAction::make()
          ->label('أرشفة'),
        RestoreAction::make()
          ->label('استعادة'),
        ForceDeleteAction::make()
          ->label('حذف نهائي')
          ->before(function (ForceDeleteAction $action, $record) {
            if ((float) $record->quantity > 0) {
              Notification::make()
                ->title('غير مسموح')
                ->body('لا يمكن حذف عملية استيراد تحتوي على كمية. يجب تصفير الكمية أولاً.')
                ->warning()
                ->send();

              $action->halt();
            }
          }),
      ])
      ->bulkActions([
        BulkActionGroup::make([
          BulkAction::make('print_selected')
            ->label('طباعة المحدد (PDF)')
            ->icon('heroicon-o-printer')
            ->color('success')
            ->visible(fn(Tables\Table $table) => !($table->getFilters()['trashed'] ?? null))
            ->action(function (Collection $records) {
              return redirect()->route('product.import.print', [
                'ids' => $records->pluck('id')->toArray()
              ]);
            })->openUrlInNewTab(),


          DeleteBulkAction::make()
            ->label('أرشفة المحدد'),
          RestoreBulkAction::make()
            ->label('استعادة المحدد'),
          ForceDeleteBulkAction::make()
            ->label('حذف نهائي للمحدد')
            ->before(function (ForceDeleteBulkAction $action, Collection $records) {
              $hasQuantity = $records->contains(fn($record) => (float) $record->quantity > 0);
              if ($hasQuantity) {
                Notification::make()
                  ->title('لا يمكن الحذف النهائي')
                  ->body('بعض السجلات المختارة لا تزال تحتوي على كميات واردة. يرجى تصفيرها قبل الحذف.')
                  ->danger()
                  ->send();
                $action->halt();
              }
            }),
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

  public static function getEloquentQuery(): Builder
  {
    return parent::getEloquentQuery()
      ->forActiveYear()
      ->withTrashed()
      ->with([
        'productImport',
        'productVariant.product',
        'user',
        'payments'
      ]);
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