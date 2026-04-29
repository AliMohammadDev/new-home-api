<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonalWithdrawalEntryResource\Pages;
use App\Filament\Resources\PersonalWithdrawalEntryResource\RelationManagers;
use App\Models\PersonalWithdrawalEntry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PersonalWithdrawalEntryResource extends Resource
{
  protected static ?string $model = PersonalWithdrawalEntry::class;

  protected static ?string $navigationIcon = 'heroicon-o-user-minus';
  protected static ?string $navigationGroup = 'الأرشيف المالي';
  protected static ?string $navigationLabel = 'سجل حركات المسحوبات';
  protected static ?string $pluralModelLabel = 'حركات المسحوبات الشخصية';
  protected static ?string $modelLabel = 'حركة مسحوبات';

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

        TextColumn::make('withdrawal.user_name')
          ->label('المستلم')
          ->searchable(),

        TextColumn::make('treasure.name')
          ->label('الصندوق')
          ->sortable(),

        TextColumn::make('amount')
          ->label('المبلغ')
          ->money('USD')
          ->color('warning')
          ->sortable(),

        TextColumn::make('note')
          ->label('البيان')
          ->searchable(),
      ])
      ->filters([
        SelectFilter::make('company_treasure_id')
          ->label('الصندوق')
          ->relationship('treasure', 'name'),
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
      'index' => Pages\ListPersonalWithdrawalEntries::route('/'),
      // 'create' => Pages\CreatePersonalWithdrawalEntry::route('/create'),
      // 'edit' => Pages\EditPersonalWithdrawalEntry::route('/{record}/edit'),
    ];
  }
}
