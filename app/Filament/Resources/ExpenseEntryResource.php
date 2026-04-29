<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseEntryResource\Pages;
use App\Filament\Resources\ExpenseEntryResource\RelationManagers;
use App\Models\ExpenseEntry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
          ->sortable(),

        TextColumn::make('expense.reason')
          ->label('المصروف المرتبط')
          ->searchable(),

        TextColumn::make('treasure.name')
          ->label('الصندوق')
          ->sortable(),

        TextColumn::make('user.name')
          ->label('بواسطة')
          ->sortable(),

        TextColumn::make('amount')
          ->label('المبلغ')
          ->money('USD')
          ->color('danger')
          ->sortable(),

        TextColumn::make('note')
          ->label('ملاحظات الحركة')
          ->limit(50)
          ->searchable(),
      ])
      ->filters([
        SelectFilter::make('company_treasure_id')
          ->label('تصفية حسب الصندوق')
          ->relationship('treasure', 'name'),

        SelectFilter::make('user_id')
          ->label('تصفية حسب الموظف')
          ->relationship('user', 'name'),
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
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
      //
    ];
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
