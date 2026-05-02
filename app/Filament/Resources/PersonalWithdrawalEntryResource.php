<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonalWithdrawalEntryResource\Pages;
use App\Models\PersonalWithdrawalEntry;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;


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
          ->searchable()
          ->sortable(),

        TextColumn::make('withdrawal.user_name')
          ->label('المستلم')
          ->searchable()
          ->searchable(),

        TextColumn::make('treasure.name')
          ->label('الصندوق')
          ->searchable()
          ->sortable(),

        TextColumn::make('amount')
          ->label('المبلغ')
          ->money('USD', locale: 'en_US')
          ->color('danger')
          ->searchable()
          ->sortable(),

        TextColumn::make('note')
          ->label('البيان')
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
      'index' => Pages\ListPersonalWithdrawalEntries::route('/'),
      // 'create' => Pages\CreatePersonalWithdrawalEntry::route('/create'),
      // 'edit' => Pages\EditPersonalWithdrawalEntry::route('/{record}/edit'),
    ];
  }
}
