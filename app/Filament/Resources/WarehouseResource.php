<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WarehouseResource\Pages;
use App\Filament\Resources\WarehouseResource\RelationManagers;
use App\Models\Warehouse;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class WarehouseResource extends Resource
{
  protected static ?string $model = Warehouse::class;
  protected static ?string $navigationIcon = 'heroicon-o-building-library';
  protected static ?int $navigationSort = 3;
  protected static ?string $navigationLabel = ' مستودعات مصغرة';
  protected static ?string $pluralModelLabel = 'مستودعات مصغرة';
  protected static ?string $modelLabel = 'مستودعات مصغرة';
  protected static ?string $navigationGroup = 'شحن و استيراد';

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Section::make()
          ->schema([
            Select::make('user_id')
              ->label('المسؤول عن المستودع')
              ->relationship('user', 'name')
              ->default(auth()->id())
              ->required()
              ->searchable()
              ->preload(),

            TextInput::make('name')
              ->label('اسم المستودع')
              ->required()
              ->maxLength(255),

            TextInput::make('phone')
              ->label('رقم الهاتف')
              ->tel()
              ->required(),

            TextInput::make('city')
              ->label('المدينة')
              ->required(),

            Textarea::make('address')
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
        TextColumn::make('name')
          ->label('الاسم')
          ->searchable()
          ->sortable(),

        TextColumn::make('user.name')
          ->label('المسؤول')
          ->searchable()
          ->sortable(),

        TextColumn::make('city')
          ->label('المدينة')
          ->badge()
          ->color('info')
          ->searchable()
          ->sortable(),

        TextColumn::make('address')
          ->label('العنوان التفصيلي')
          ->limit(30)
          ->sortable(),
        TextColumn::make('phone')
          ->label('الهاتف')
          ->copyable(),

        TextColumn::make('created_at')
          ->label('تاريخ الإضافة')
          ->dateTime('Y-m-d H:i')
          ->timezone('Asia/Riyadh')
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->defaultSort('created_at', 'DESC')
      ->filters([
        SelectFilter::make('city')
          ->label('تصفية حسب المدينة')
          ->options(fn() => Warehouse::pluck('city', 'city')->unique()->toArray()),

        SelectFilter::make('user_id')
          ->label('تصفية حسب المسؤول')
          ->relationship('user', 'name')
          ->visible(fn() => auth()->user()->hasRole('super_admin'))
          ->searchable()
          ->preload(),
      ])
      ->actions([
        EditAction::make(),
        DeleteAction::make()
          ->before(function (DeleteAction $action, Warehouse $record) {
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
        BulkActionGroup::make([
          DeleteBulkAction::make(),
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
    $user = auth()->user();

    if ($user->hasRole('super_admin')) {
      return $query;
    }

    if ($user->hasRole('main_warehouse_manager')) {
      return $query;
    }

    if ($user->hasRole('sub_warehouse_manager')) {
      return $query->where('user_id', $user->id);
    }

    return $query->whereRaw('1 = 0');
  }
}
