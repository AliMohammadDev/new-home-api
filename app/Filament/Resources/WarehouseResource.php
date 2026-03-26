<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WarehouseResource\Pages;
use App\Filament\Resources\WarehouseResource\RelationManagers;
use App\Models\Warehouse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WarehouseResource extends Resource
{
  protected static ?string $model = Warehouse::class;
  protected static ?string $navigationIcon = 'heroicon-o-building-library';
  protected static ?string $navigationLabel = ' مستودعات مصغرة';
  protected static ?string $pluralModelLabel = 'مستودعات مصغرة';
  protected static ?string $modelLabel = 'مستودعات مصغرة';
  protected static ?string $navigationGroup = 'شحن و استيراد';

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make()
          ->schema([
            Forms\Components\Select::make('user_id')
              ->label('المسؤول عن المستودع')
              ->relationship('user', 'name')
              ->default(auth()->id())
              ->required()
              ->searchable()
              ->preload(),

            Forms\Components\TextInput::make('name')
              ->label('اسم المستودع')
              ->required()
              ->maxLength(255),

            Forms\Components\TextInput::make('phone')
              ->label('رقم الهاتف')
              ->tel()
              ->required(),

            Forms\Components\TextInput::make('city')
              ->label('المدينة')
              ->required(),

            Forms\Components\Textarea::make('address')
              ->label('العنوان التفصيلي')
              ->required()
              ->columnSpanFull(),
          ])
          ->columns(2),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('name')
          ->label('الاسم')
          ->searchable()
          ->sortable(),

        Tables\Columns\TextColumn::make('user.name')
          ->label('المسؤول')
          ->searchable()
          ->sortable(),

        Tables\Columns\TextColumn::make('city')
          ->label('المدينة')
          ->badge()
          ->color('info')
          ->searchable()
          ->sortable(),

        Tables\Columns\TextColumn::make('address')
          ->label('العنوان التفصيلي')
          ->limit(30)
          ->sortable(),
        Tables\Columns\TextColumn::make('phone')
          ->label('الهاتف')
          ->copyable(),

        Tables\Columns\TextColumn::make('created_at')
          ->label('تاريخ الإضافة')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->defaultSort('created_at', 'DESC')
      ->filters([
        Tables\Filters\SelectFilter::make('city')
          ->label('تصفية حسب المدينة')
          ->options(fn() => \App\Models\Warehouse::pluck('city', 'city')->unique()->toArray()),

        Tables\Filters\SelectFilter::make('user_id')
          ->label('تصفية حسب المسؤول')
          ->relationship('user', 'name')
          ->visible(fn() => auth()->user()->hasRole('super_admin'))
          ->searchable()
          ->preload(),
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make()
          ->before(function (Tables\Actions\DeleteAction $action, Warehouse $record) {
            if ($record->productVariants()->exists()) {
              \Filament\Notifications\Notification::make()
                ->danger()
                ->title('لا يمكن حذف المستودع')
                ->body('هذا المستودع يحتوي على مخزون بضائع حالياً. يجب تفريغ المستودع أو نقله قبل الحذف.')
                ->persistent()
                ->send();

              $action->halt();
            }
          }),
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
      RelationManagers\ProductVariantsRelationManager::class,
    ];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListWarehouses::route('/'),
      'create' => Pages\CreateWarehouse::route('/create'),
      'edit' => Pages\EditWarehouse::route('/{record}/edit'),
    ];
  }

  public static function getEloquentQuery(): Builder
  {
    $query = parent::getEloquentQuery()->with(['user']);

    if (auth()->user()->hasRole('super_admin')) {
      return $query;
    }

    return $query->where('user_id', auth()->id());
  }
}
