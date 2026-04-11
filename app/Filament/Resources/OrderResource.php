<?php

namespace App\Filament\Resources;

use App\Filament\Exports\OrderExporter;
use App\Filament\Resources\OrderResource\RelationManagers\OrderItemsRelationManager;
use App\Filament\Resources\OrderResource\Pages;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use App\Models\Order;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Actions\Exports\Enums\ExportFormat;

class OrderResource extends Resource
{
  protected static ?string $model = Order::class;
  protected static ?string $navigationIcon = 'heroicon-o-receipt-refund';
  protected static ?string $navigationLabel = 'الطلبات';
  protected static ?string $pluralModelLabel = 'الطلبات';
  protected static ?string $modelLabel = 'طلب';
  protected static ?string $navigationGroup = 'إدارة الطلبات';

  public static function canCreate(): bool
  {
    return false;
  }

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make('تعديل حالة الطلب')
          ->description('يمكنك فقط تغيير حالة الطلب من هنا')
          ->schema([
            Forms\Components\Select::make('status')
              ->label('حالة الطلب')
              ->options([
                'pending' => 'Pending (قيد الانتظار)',
                'completed' => 'Completed (اكتمال)',
                'cancelled' => 'Cancelled (الغاء)',
              ])
              ->required()
              ->native(false),


            Forms\Components\Select::make('delivery_company_id')
              ->label('شركة التوصيل')
              ->relationship('deliveryCompany', 'name')
              ->searchable()
              ->preload()
              ->required()
              ->disabled(!auth()->user()->hasAnyRole(['super_admin', 'main_warehouse_manager'])),

            Forms\Components\TextInput::make('shipping_fee')
              ->label('رسوم الشحن')
              ->numeric()
              ->prefix('$')
              ->default(0)
              ->live(onBlur: true)
              ->afterStateUpdated(fn(Forms\Set $set, Forms\Get $get, $record) => self::updateTotal($set, $get, $record))
              ->disabled(!auth()->user()->hasAnyRole(['super_admin', 'main_warehouse_manager'])),


            Forms\Components\TextInput::make('delivery_fee')
              ->label('رسوم التوصيل')
              ->numeric()
              ->prefix('$')
              ->default(0)
              ->live(onBlur: true)
              ->afterStateUpdated(fn(Forms\Set $set, Forms\Get $get, $record) => self::updateTotal($set, $get, $record))
              ->disabled(!auth()->user()->hasAnyRole(['super_admin', 'main_warehouse_manager'])),



            Forms\Components\TextInput::make('total_amount')
              ->label('الإجمالي النهائي')
              ->numeric()
              ->prefix('$')
              ->readOnly()
              ->afterStateHydrated(function (Forms\Set $set, Forms\Get $get, $record) {
                if ($record) {
                  $itemsSubtotal = floatval($record->orderItems->sum('total'));
                  $shipping = floatval($get('shipping_fee') ?? 0);
                  $delivery = floatval($get('delivery_fee') ?? 0);
                  $set('total_amount', round($itemsSubtotal + $shipping + $delivery, 2));
                }
              }),
          ])->columns(2)


      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->query(
        Order::query()
          ->with(['user', 'deliveryCompany'])
          ->withSum('orderItems as items_subtotal_sum', 'total')
      )
      ->columns([
        TextColumn::make('id')
          ->label('رقم الطلب')
          ->sortable()
          ->searchable(),
        TextColumn::make('user.name')
          ->label('المستخدم')
          ->sortable()
          ->searchable(),

        TextColumn::make('deliveryCompany.name')
          ->label('شركة التوصيل')
          ->badge()
          ->color('primary')
          ->icon('heroicon-m-truck')
          ->placeholder('لم تحدد بعد')
          ->sortable()
          ->searchable(),
        TextColumn::make('status')
          ->label('الحالة')
          ->badge()
          ->colors([
            'warning' => 'pending',
            'success' => 'completed',
            'danger' => 'cancelled',
          ]),

        TextColumn::make('payment_method')
          ->label('طريقة الدفع')
          ->sortable()
          ->searchable(),

        TextColumn::make('items_subtotal_sum')
          ->label('صافي المنتجات')
          // ->getStateUsing(function (Order $record) {
          //   return $record->orderItems->sum('total');
          // })
          ->money('USD', locale: 'en_US')
          ->color('success')
          ->sortable(),

        TextColumn::make('total_amount')
          ->label('المبلغ الكلي')
          ->money('USD', locale: 'en_US')
          ->color('success')
          ->sortable()
          ->searchable(),


        TextColumn::make('order_items_count')
          ->label('عدد المنتجات')
          ->counts('orderItems'),
        TextColumn::make('created_at')
          ->label('تاريخ الطلب')

          ->dateTime('Y-m-d H:i')
          ->timezone('Asia/Riyadh')
          ->sortable(),
      ])
      ->defaultSort('created_at', 'desc')

      ->filters([
        Tables\Filters\SelectFilter::make('status')
          ->label('الحالة')
          ->options([
            'pending' => 'قيد الانتظار',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
          ]),

        Tables\Filters\TrashedFilter::make()
          ->label('حالة السجلات')
          ->falseLabel('السجلات المؤرشفة فقط')
          ->trueLabel('السجلات النشطة فقط')
          ->placeholder('الكل')
          ->native(false),

      ])
      ->actions([
        Tables\Actions\ViewAction::make()
          ->label('عرض التفاصيل')
          ->color('info')
          ->icon('heroicon-m-eye'),

        Tables\Actions\EditAction::make()
          ->label('تأكيد الطلب')
          ->color('warning')
          ->icon('heroicon-m-check-circle'),

        Tables\Actions\DeleteAction::make()
          ->label('أرشفة'),
        Tables\Actions\RestoreAction::make()
          ->label('استعادة'),
        Tables\Actions\ForceDeleteAction::make()
          ->label('حذف نهائي')
          ->before(function (Tables\Actions\ForceDeleteAction $action, $record) {
            if ($record->total_amount != 0) {
              Notification::make()
                ->title('غير مسموح')
                ->body('يجب تصفير المبلغ أولاً قبل الحذف النهائي.')
                ->warning()
                ->send();

              $action->halt();
            }
          }),
      ])
      ->recordUrl(
        fn(Order $record): string => Pages\ViewOrder::getUrl([$record->id]),
      )
      ->bulkActions([
        Tables\Actions\DeleteBulkAction::make()
          ->label('أرشفة المحدد'),
        Tables\Actions\RestoreBulkAction::make()
          ->label('استعادة المحدد'),
        Tables\Actions\ForceDeleteBulkAction::make()
          ->label('حذف نهائي للمحدد')
          ->before(function (Tables\Actions\ForceDeleteBulkAction $action, \Illuminate\Database\Eloquent\Collection $records) {
            $invalidRecords = $records->where('total_amount', '!=', 0);
            if ($invalidRecords->count() > 0) {
              Notification::make()
                ->title('لا يمكن الحذف النهائي')
                ->body('بعض السجلات المختارة تحتوي على مبالغ غير صفرية. يجب تصفير المبالغ أولاً.')
                ->danger()
                ->send();
              $action->halt();
            }
          }),
        ExportBulkAction::make()->exporter(OrderExporter::class)->color('success')->icon('heroicon-o-arrow-down-tray')->formats([ExportFormat::Csv, ExportFormat::Xlsx]),
      ])
      ->headerActions([
        ExportAction::make()->exporter(OrderExporter::class)->color('success')->icon('heroicon-o-arrow-down-tray')
          ->formats([ExportFormat::Csv, ExportFormat::Xlsx]),
      ]);

  }


  public static function getRelations(): array
  {
    return [
      OrderItemsRelationManager::class,
    ];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListOrders::route('/'),
      'view' => Pages\ViewOrder::route('/{record}'),
      'edit' => Pages\EditOrder::route('/{record}/edit'),
    ];
  }

  public static function updateTotal(Forms\Set $set, Forms\Get $get, $record)
  {
    if (!$record)
      return;

    $itemsSubtotal = floatval($record->orderItems->sum('total'));

    $shipping = floatval($get('shipping_fee') ?? 0);
    $delivery = floatval($get('delivery_fee') ?? 0);

    $total = round($itemsSubtotal + $shipping + $delivery, 2);

    $set('total_amount', $total);
  }

  public static function infolist(Infolist $infolist): Infolist
  {
    return $infolist->schema([
      Section::make('معلومات الطلب والشحن')
        ->schema([
          TextEntry::make('id')->label('رقم الطلب'),

          TextEntry::make('status')
            ->label('الحالة')
            ->badge()
            ->color(fn(string $state): string => match ($state) {
              'pending' => 'warning',
              'completed' => 'success',
              'cancelled' => 'danger',
              default => 'gray',
            })
            ->formatStateUsing(fn(string $state): string => match ($state) {
              'pending' => 'قيد الانتظار',
              'completed' => 'مكتمل',
              'cancelled' => 'ملغي',
              default => $state,
            })
            ->hintAction(
              \Filament\Infolists\Components\Actions\Action::make('updateStatus')
                ->label('تغيير الحالة والشحن')
                ->icon('heroicon-m-pencil-square')
                ->color('info')
                ->fillForm(fn(Order $record): array => [
                  'status' => $record->status,
                  'delivery_company_id' => $record->delivery_company_id,
                ])
                ->form([
                  Forms\Components\Select::make('status')
                    ->label('اختر الحالة الجديدة')
                    ->options([
                      'pending' => 'قيد الانتظار',
                      'completed' => 'مكتمل',
                      'cancelled' => 'ملغي',
                    ])
                    ->required()
                    ->native(false),

                  Forms\Components\Select::make('delivery_company_id')
                    ->label('شركة التوصيل')
                    ->relationship('deliveryCompany', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled(!auth()->user()->hasAnyRole(['super_admin', 'main_warehouse_manager'])),

                ])
                ->action(function (Order $record, array $data) {
                  $record->update($data);

                  \Filament\Notifications\Notification::make()
                    ->title('تم تحديث الطلب بنجاح')
                    ->success()
                    ->send();
                })
            ),

          TextEntry::make('deliveryCompany.name')
            ->label('شركة التوصيل')
            ->icon('heroicon-m-truck')
            ->weight('bold')
            ->color('primary')
            ->placeholder('لم تحدد بعد'),

          TextEntry::make('shipping_fee')
            ->label('رسوم الشحن')
            ->money('USD', locale: 'en_US'),

          TextEntry::make('delivery_fee')
            ->label('رسوم التوصيل')
            ->money('USD', locale: 'en_US'),

          TextEntry::make('total_amount')
            ->label('المبلغ الكلي الكلي')
            ->weight('bold')
            ->color('success')
            ->money('USD', locale: 'en_US'),
          // -----------------------------------

          TextEntry::make('payment_method')->label('طريقة الدفع'),
          TextEntry::make('created_at')->label('تاريخ الطلب')->since(),
        ])
        ->columns(2),

      Section::make('معلومات العميل')
        ->schema([
          TextEntry::make('user.name')->label('الاسم'),
          TextEntry::make('user.email')->label('البريد الإلكتروني'),
        ])
        ->columns(2),

      Section::make('معلومات العنوان التفصيلية (Checkout)')
        ->schema([
          TextEntry::make('full_name')
            ->label('الاسم الكامل المستلم')
            ->getStateUsing(function ($record) {
              if (!$record->checkout)
                return '-';
              return "{$record->checkout->first_name} {$record->checkout->last_name}";
            }),
          TextEntry::make('checkout.phone')->label('رقم الهاتف'),
          TextEntry::make('checkout.city')->label('المدينة'),
          TextEntry::make('checkout.floor')->label('الطابق'),
          TextEntry::make('checkout.street')->label('الشارع'),
          TextEntry::make('checkout.postal_code')->label('الرمز البريدي'),
        ])
        ->columns(2),
    ]);
  }

  public static function getEloquentQuery(): Builder
  {
    $query = parent::getEloquentQuery();

    if (auth()->user()->hasRole('super_admin')) {
      return $query;
    }
    if (auth()->user()->hasRole('main_warehouse_manager')) {
      return $query;
    }

    return $query->whereHas('deliveryCompany', function (Builder $subQuery) {
      $subQuery->where('user_id', auth()->id());
    });
  }
}
