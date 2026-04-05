<?php

namespace App\Filament\Resources\SalesPointResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CashierRelationManager extends RelationManager
{
  protected static string $relationship = 'cashier';
  protected static ?string $title = 'موظفي الكاشير';
  protected static ?string $modelLabel = 'كاشير';

  public function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Select::make('user_id')
          ->label('اختر الموظف')
          ->relationship('user', 'name')
          ->searchable()
          ->preload()
          ->required(),

        Forms\Components\Select::make('shift_type')
          ->label('نوع الشفت')
          ->options([
            'morning' => 'صباحي',
            'evening' => 'مسائي',
            'night' => 'ليلي',
          ])
          ->required(),


      ]);
  }

  public function table(Table $table): Table
  {
    return $table
      ->recordTitleAttribute('user.name')
      ->recordUrl(
        fn(\App\Models\SalesPointCashier $record): string =>
        "/admin/sales-point-cashiers/{$record->id}/edit"
      )
      ->columns([
        Tables\Columns\TextColumn::make('user.name')
          ->label('اسم الكاشير')
          ->icon('heroicon-m-user-circle')
          ->weight('bold')
          ->searchable(),

        Tables\Columns\TextColumn::make('shift_type')
          ->label('الشفت')
          ->badge()
          ->formatStateUsing(fn(string $state): string => match ($state) {
            'morning' => 'صباحي',
            'evening' => 'مسائي',
            'night' => 'ليلي',
            default => $state,
          })
          ->color(fn(string $state): string => match ($state) {
            'morning' => 'warning',
            'evening' => 'primary',
            'night' => 'gray',
            default => 'gray',
          }),

        Tables\Columns\TextColumn::make('daily_limit')
          ->label('الحد اليومي')
          ->color('primary')
          ->money('USD', locale: 'en_US'),

        Tables\Columns\TextColumn::make('user.email')
          ->label('الايميل')
          ->color('gray')
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        Tables\Filters\SelectFilter::make('shift_type')
          ->label('تصفية حسب الشفت')
          ->options([
            'morning' => 'صباحي',
            'evening' => 'مسائي',
            'night' => 'ليلي',
          ]),
      ])
      ->headerActions([
        Tables\Actions\CreateAction::make()
          ->label('إضافة كاشير جديد'),
      ])
      ->actions([
      ]);
  }
}