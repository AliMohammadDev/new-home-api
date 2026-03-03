<?php

namespace App\Filament\Resources\SalesPointCashierResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FatoraRelationManager extends RelationManager
{
  protected static string $relationship = 'fatora';

  public function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\TextInput::make('id')
          ->required()
          ->maxLength(255),
      ]);
  }

  public function table(Table $table): Table
  {
    return $table
      ->recordTitleAttribute('id')
      ->columns([
        Tables\Columns\TextColumn::make('id')
          ->label('رقم الفاتورة')
          ->sortable(),

        Tables\Columns\TextColumn::make('date')
          ->label('التاريخ')
          ->date()
          ->sortable(),

        Tables\Columns\TextColumn::make('full_price')
          ->label('إجمالي الفاتورة')
          ->money('USD', locale: 'en_US'),
      ])
      ->filters([])
      ->headerActions([
      ])
      ->actions([
      ]);
  }
}
