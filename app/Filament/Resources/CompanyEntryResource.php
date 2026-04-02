<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyEntryResource\Pages;
use App\Filament\Resources\CompanyEntryResource\RelationManagers;
use App\Models\CompanyEntry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompanyEntryResource extends Resource
{
  protected static ?string $model = CompanyEntry::class;

  protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
  protected static ?string $navigationGroup = 'الإدارة المالية';
  protected static ?string $navigationLabel = 'حركات الصناديق';
  protected static ?string $pluralModelLabel = 'سجل حركات الصناديق';
  protected static ?string $modelLabel = 'حركة مالية';
  protected static ?int $navigationSort = 2;

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make('تفاصيل الحركة المالية')
          ->schema([
            Forms\Components\Select::make('company_treasure_id')
              ->label('الصندوق')
              ->relationship('treasure', 'name')
              ->required()
              ->searchable()
              ->preload(),

            Forms\Components\Select::make('user_id')
              ->label('الموظف المسؤول')
              ->relationship('user', 'name')
              ->default(auth()->id())
              ->required()
              ->searchable(),

            Forms\Components\Select::make('trans_type')
              ->label('نوع العملية')
              ->options([
                'deposit' => 'دائن',
                'withdraw' => 'مدين',
              ])
              ->required()
              ->native(false),

            Forms\Components\TextInput::make('amount')
              ->label('المبلغ')
              ->numeric()
              ->required()
              ->prefix('USD'),

            Forms\Components\TextInput::make('name')
              ->label('البيان / السبب')
              ->required()
              ->columnSpanFull()
              ->placeholder('مثال: دفعة من مورد، مصاريف نثرية...'),
          ])->columns(2),
      ]);
  }
  public static function table(Table $table): Table
  {
    return $table->columns([
      Tables\Columns\TextColumn::make('created_at')->label('التاريخ')->dateTime(),
      Tables\Columns\TextColumn::make('treasure.name')
        ->label('الصندوق')
        ->searchable()
        ->sortable(),

      Tables\Columns\TextColumn::make('user.name')
        ->label('الموظف')
        ->searchable()
        ->sortable(),

      Tables\Columns\TextColumn::make('trans_type')
        ->label('نوع العملية')
        ->badge()
        ->formatStateUsing(fn(string $state): string => match ($state) {
          'deposit' => 'دائن',
          'withdraw' => 'مدين',
          default => $state,
        })
        ->color(fn(string $state): string => match ($state) {
          'deposit' => 'success',
          'withdraw' => 'danger',
          default => 'gray',
        })
        ->icon(fn(string $state): string => match ($state) {
          'deposit' => 'heroicon-m-arrow-trending-up',
          'withdraw' => 'heroicon-m-arrow-trending-down',
          default => 'heroicon-m-minus',
        }),


      Tables\Columns\TextColumn::make('name')
        ->label('البيان')
        ->searchable(),
      Tables\Columns\TextColumn::make('amount')
        ->label('المبلغ')
        ->money('USD', locale: 'en_US')
        ->summarize([
          Tables\Columns\Summarizers\Sum::make()
            ->label('الإجمالي')
            ->money('USD', locale: 'en_US'),
        ]),
    ])
      ->defaultSort('created_at', 'DESC')
      ->filters([
        Tables\Filters\SelectFilter::make('trans_type')
          ->label('نوع الحركة')
          ->options([
            'deposit' => 'دائن',
            'withdraw' => 'مدين',
          ]),
        Tables\Filters\SelectFilter::make('company_treasure_id')
          ->label('الصندوق')
          ->relationship('treasure', 'name'),
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make()
          ->requiresConfirmation()
          ->modalHeading('حذف الحركة المالية')
          ->modalDescription('عند حذف الحركة، سيتم إعادة الرصيد إلى ما كان عليه في الصندوق. هل أنت متأكد؟'),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ])
    ;
  }
  public static function getRelations(): array
  {
    return [
      //
    ];
  }

  public static function getEloquentQuery(): Builder
  {
    return parent::getEloquentQuery()
      ->with(['treasure', 'user']);
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListCompanyEntries::route('/'),
      'create' => Pages\CreateCompanyEntry::route('/create'),
      'edit' => Pages\EditCompanyEntry::route('/{record}/edit'),
    ];
  }
}
