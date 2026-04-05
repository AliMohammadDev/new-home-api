<?php

namespace App\Filament\Resources\ProductVariantResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use App\Filament\Resources\WarehouseResource;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Table;
use App\Models\Warehouse;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Tables;

class WarehousesRelationManager extends RelationManager
{
  protected static string $relationship = 'warehouses';
  protected static ?string $title = 'توزع المخزون في المستودعات';

  public function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\TextInput::make('name')
          ->required()
          ->maxLength(255),
      ]);
  }

  public function table(Table $table): Table
  {
    return $table
      ->recordTitleAttribute('name')
      ->recordUrl(
        fn(Model $record): string => WarehouseResource::getUrl('edit', ['record' => $record])
      )
      ->columns([
        Tables\Columns\TextColumn::make('name')
          ->label('اسم المستودع')
          ->color('primary')
          ->weight('bold')
          ->searchable(),

        Tables\Columns\TextColumn::make('user.name')
          ->label('المسؤول')
          ->icon('heroicon-m-user')
          ->searchable(),

        Tables\Columns\TextColumn::make('phone')
          ->label('رقم الهاتف')
          ->copyable()
          ->icon('heroicon-m-phone'),

        Tables\Columns\TextColumn::make('city')
          ->label('المدينة')
          ->badge()
          ->color('info'),

        Tables\Columns\TextColumn::make('address')
          ->label('العنوان التفصيلي')
          ->limit(30)
          ->tooltip(fn(Model $record): string => $record->address),

        Tables\Columns\TextColumn::make('pivot.amount')
          ->label('الكمية المتوفرة')
          ->badge()
          ->color('success')
          ->weight('bold')
          ->suffix(' قطعة'),
      ])
      ->filters([
        Tables\Filters\SelectFilter::make('city')
          ->label('تصفية حسب المدينة')
          ->options(fn() => Warehouse::pluck('city', 'city')->unique()->toArray()),
      ])
      ->actions([
        Tables\Actions\EditAction::make()
          ->url(fn(Model $record): string => WarehouseResource::getUrl('edit', ['record' => $record])),
        Tables\Actions\DeleteAction::make(),
      ]);
  }
}