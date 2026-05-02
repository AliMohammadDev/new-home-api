<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanySalesTransferEntryResource\Pages;
use App\Models\CompanySalesTransferEntry;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;

class CompanySalesTransferEntryResource extends Resource
{
  protected static ?string $model = CompanySalesTransferEntry::class;
  protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
  protected static ?string $navigationGroup = 'الأرشيف المالي';
  protected static ?string $navigationLabel = 'سجل حركات تحويلات النقاط';
  protected static ?string $pluralModelLabel = 'حركات تحويلات نقاط البيع';

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

        TextColumn::make('transfer.salesPoint.name')
          ->label('نقطة البيع')
          ->searchable()
          ->sortable(),

        TextColumn::make('treasure.name')
          ->label('من خزينة')
          ->searchable()
          ->sortable(),

        TextColumn::make('amount')
          ->label('الكمية المحولة')
          ->searchable()
          ->sortable()
          ->color('warning'),

        TextColumn::make('user.name')
          ->label('الموظف')
          ->searchable()
          ->sortable(),
      ])
      ->filters([
        //
      ])
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
      'index' => Pages\ListCompanySalesTransferEntries::route('/'),

    ];
  }
}
