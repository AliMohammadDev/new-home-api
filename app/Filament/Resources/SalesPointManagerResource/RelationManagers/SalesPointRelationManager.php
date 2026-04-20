<?php

namespace App\Filament\Resources\SalesPointManagerResource\RelationManagers;

use App\Filament\Resources\SalesPointResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SalesPointRelationManager extends RelationManager
{
  protected static string $relationship = 'salesPoint';
  protected static ?string $title = 'بيانات نقطة البيع المشرف عليها';
  protected static ?string $modelLabel = 'نقطة بيع';

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
        fn(Model $record): string => SalesPointResource::getUrl('edit', ['record' => $record])
      )
      ->columns([
        Tables\Columns\TextColumn::make('name')
          ->label('اسم نقطة البيع')
          ->weight('bold')
          ->color('primary')
          ->icon('heroicon-m-home-modern'),

        Tables\Columns\TextColumn::make('location')
          ->label('الموقع')
          ->icon('heroicon-m-map-pin'),

        Tables\Columns\TextColumn::make('phone')
          ->label('رقم هاتف النقطة')
          ->copyable(),

        Tables\Columns\TextColumn::make('warehouse.name')
          ->label('المستودع التابع له')
          ->badge()
          ->color('gray'),

        Tables\Columns\TextColumn::make('amount')
          ->label('الرصيد المالي')
          ->money('USD', locale: 'en_US')
          ->color('success')
          ->weight('bold'),


      ])
      ->actions([

      ]);
  }
}
