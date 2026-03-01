<?php

namespace App\Filament\Resources\SalesPointResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManagersRelationManager extends RelationManager
{

  protected static string $relationship = 'managers';
  protected static ?string $title = 'مدراء نقطة البيع';
  protected static ?string $modelLabel = 'مدير';

  public function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Select::make('user_id')
          ->label('اختر المستخدم')
          ->relationship('user', 'name')
          ->searchable()
          ->preload()
          ->required(),

        Forms\Components\Toggle::make('is_primary')
          ->label('مدير أساسي')
          ->onColor('success')
          ->default(false),
      ]);
  }

  public function table(Table $table): Table
  {
    return $table
      ->recordTitleAttribute('name')
      ->columns([
        Tables\Columns\TextColumn::make('name')
          ->label('اسم المدير')
          ->icon('heroicon-m-user')
          ->searchable(),

        Tables\Columns\TextColumn::make('email')
          ->label('البريد الإلكتروني')
          ->copyable(),

        Tables\Columns\IconColumn::make('is_primary')
          ->label('أساسي')
          ->boolean(),
      ])
      ->filters([])
      ->headerActions([

      ])
      ->actions([
        Tables\Actions\DetachAction::make()->label('إزالة'),
      ])
      ->bulkActions([
        Tables\Actions\DetachBulkAction::make(),
      ]);
  }
}
