<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CartResource\Pages;
use App\Filament\Resources\CartResource\RelationManagers\CartItemsRelationManager;
use App\Models\Cart;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Actions\ViewAction;

class CartResource extends Resource
{
  protected static ?string $model = Cart::class;
  protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
  protected static ?int $navigationSort = 2;
  protected static ?string $navigationLabel = 'السلة';
  protected static ?string $pluralModelLabel = 'السلة';
  protected static ?string $modelLabel = 'سلة';
  protected static ?string $navigationGroup = 'إدارة الطلبات';

  public static function canCreate(): bool
  {
    return false;
  }

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        //
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('id')
          ->label('ID')
          ->sortable()
          ->searchable(),

        TextColumn::make('user.name')
          ->label('المستخدم')
          ->sortable()
          ->searchable(['users.name']),

        TextColumn::make('status')
          ->label('الحالة')
          ->badge()
          ->color(fn(string $state) => match ($state) {
            'active' => 'success',
            'pending' => 'warning',
            'completed' => 'gray',
            default => 'secondary',
          }),

        TextColumn::make('cart_items_count')
          ->label('عدد المنتجات')
          ->counts('cartItems')
          ->sortable(),

        TextColumn::make('created_at')
          ->label('تاريخ الإنشاء')
          ->dateTime('Y-m-d H:i')
          ->timezone('Asia/Riyadh')
          ->sortable()
          ->searchable()
          ->dateTime(),
      ])
      ->defaultSort('created_at', 'desc')
      ->actions([
        ViewAction::make(),
      ])
      ->bulkActions([])
      ->filters([
        SelectFilter::make('status')
          ->label('الحالة')
          ->options([
            'active' => 'نشط',
            'pending' => 'قيد الانتظار',
            'completed' => 'مكتمل',
          ]),
      ]);
  }

  public static function getRelations(): array
  {
    return [
      CartItemsRelationManager::class,
    ];
  }


  public static function getPages(): array
  {
    return [
      'index' => Pages\ListCarts::route('/'),
      'view' => Pages\ViewCart::route('/{record}'),
    ];
  }

}