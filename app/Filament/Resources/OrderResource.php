<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\OrderResource\RelationManagers\OrderItemsRelationManager;
use App\Filament\Resources\OrderResource\Widgets\OrdersCountWidget;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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




}