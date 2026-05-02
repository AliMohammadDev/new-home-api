<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseEntryResource\Pages;
use App\Models\ExpenseEntry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;



class ExpenseEntryResource extends Resource
{
  protected static ?string $model = ExpenseEntry::class;
  protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
  protected static ?string $navigationGroup = 'الأرشيف المالي';
  protected static ?string $navigationLabel = 'سجل حركات المصاريف';
  protected static ?string $pluralModelLabel = 'حركات المصاريف';
  protected static ?string $modelLabel = 'حركة مصروف';

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
        TextColumn::make('created_at')
          ->label('التاريخ')
          ->dateTime()
          ->searchable()
          ->sortable(),

        TextColumn::make('expense.reason')
          ->label('المصروف المرتبط')
          ->searchable(),

        TextColumn::make('treasure.name')
          ->label('الصندوق')
          ->searchable()
          ->sortable(),

        TextColumn::make('user.name')
          ->label('بواسطة')
          ->searchable()
          ->sortable(),

        TextColumn::make('amount')
          ->label('المبلغ')
          ->money('USD', locale: 'en_US')
          ->color('danger')
          ->searchable()
          ->sortable(),

        TextColumn::make('note')
          ->label('ملاحظات الحركة')
          ->limit(50)
          ->searchable()
          ->searchable(),
      ])
      ->filters([])
      ->defaultSort('created_at', 'DESC')
      ->actions([])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([]),
      ]);
  }

  public static function getEloquentQuery(): Builder
  {
    return parent::getEloquentQuery()
      ->forActiveYear();
  }

  public static function getRelations(): array
  {
    return [
      //
    ];
  }
  public static function canCreate(): bool
  {
    return false;
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListExpenseEntries::route('/'),
      // 'create' => Pages\CreateExpenseEntry::route('/create'),
      // 'edit' => Pages\EditExpenseEntry::route('/{record}/edit'),
    ];
  }
}
