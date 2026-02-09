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
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
        Forms\Components\Card::make()
          ->schema([
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
          ])->columns(2),
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

        Tables\Columns\TextColumn::make('city')
          ->label('المدينة')
          ->badge()
          ->color('info')
          ->sortable(),

        Tables\Columns\TextColumn::make('address')
          ->label('العنوان التفصيلي')
          ->sortable(),
        Tables\Columns\TextColumn::make('phone')
          ->label('الهاتف')
          ->copyable(),

        Tables\Columns\TextColumn::make('created_at')
          ->label('تاريخ الإضافة')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
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
}
