<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers\OrderItemsRelationManager;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

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
          ])
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('id')
          ->label('رقم الطلب')
          ->sortable()
          ->searchable(),
        TextColumn::make('user.name')
          ->label('المستخدم')
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

        TextColumn::make('total_amount')
          ->label('المبلغ الكلي')
          ->formatStateUsing(fn($state) => number_format($state, 2, '.', ','))
          ->sortable()
          ->searchable(),
        TextColumn::make('order_items_count')->label('عدد المنتجات')->counts('orderItems'),
        TextColumn::make('created_at')->label('تاريخ الطلب')->since()->sortable()->searchable(),
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
      ])
      ->actions([
        Tables\Actions\ViewAction::make(),
        Tables\Actions\EditAction::make(),
      ])
      ->recordUrl(
        fn(Order $record): string => Pages\ViewOrder::getUrl([$record->id]),
      )
      ->bulkActions([
        Tables\Actions\DeleteBulkAction::make(),
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

  public static function infolist(Infolist $infolist): Infolist
  {
    return $infolist->schema([
      Section::make('معلومات الطلب')
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
                ->label('تغيير الحالة')
                ->icon('heroicon-m-pencil-square')
                ->color('info')
                ->form([
                  Forms\Components\Select::make('status')
                    ->label('اختر الحالة الجديدة')
                    ->options([
                      'pending' => 'قيد الانتظار',
                      'completed' => 'مكتمل',
                      'cancelled' => 'ملغي',
                    ])
                    ->required(),
                ])
                ->action(function (Order $record, array $data) {
                  $record->update($data);
                })
            ),

          TextEntry::make('payment_method')->label('طريقة الدفع'),
          TextEntry::make('total_amount')
            ->label('المبلغ الكلي')
            ->formatStateUsing(fn($state) => number_format($state, 2)),
          TextEntry::make('created_at')->label('تاريخ الطلب')->since(),
        ])
        ->columns(2),

      Section::make('معلومات العميل')
        ->schema([
          TextEntry::make('user.name')->label('الاسم'),
          TextEntry::make('user.email')->label('البريد الإلكتروني'),
        ])
        ->columns(2),

      Section::make('معلومات الشحن')
        ->schema([
          TextEntry::make('full_name')
            ->label('الاسم الكامل')
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


}
