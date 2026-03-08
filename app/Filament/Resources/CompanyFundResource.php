<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyFundResource\Pages;
use App\Filament\Resources\CompanyFundResource\RelationManagers;
use App\Models\CompanyFund;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompanyFundResource extends Resource
{
  protected static ?string $model = CompanyFund::class;

  protected static ?string $navigationIcon = 'heroicon-o-banknotes';
  protected static ?string $navigationGroup = 'الإدارة المالية';
  protected static ?string $navigationLabel = 'صندوق الشركة';
  protected static ?string $pluralModelLabel = 'صناديق الشركة';
  protected static ?string $modelLabel = 'صندوق';

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make('بيانات الصندوق')
          ->schema([
            Forms\Components\TextInput::make('name')
              ->label('اسم الصندوق')
              ->required()
              ->maxLength(255),

            Forms\Components\TextInput::make('balance')
              ->label('الرصيد الحالي')
              ->numeric()
              ->default(0)
              ->prefix('USD')
              ->dehydrated(),
          ])->columns(2),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('name')
          ->label('اسم الصندوق')
          ->searchable()
          ->sortable(),

        Tables\Columns\TextColumn::make('balance')
          ->label('الرصيد المتوفر')
          ->money('USD', locale: 'en_US')
          ->sortable()
          ->color(fn($state) => $state >= 0 ? 'success' : 'danger')
          ->weight('bold'),

        Tables\Columns\TextColumn::make('updated_at')
          ->label('آخر تحديث')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->actions([
        Tables\Actions\Action::make('deposit')
          ->label('إيداع نقدية')
          ->icon('heroicon-m-plus-circle')
          ->color('success')
          ->form([
            Forms\Components\TextInput::make('amount')
              ->label('المبلغ المودع')
              ->numeric()
              ->required()
              ->minValue(1),
          ])
          ->action(fn(CompanyFund $record, array $data) => $record->increment('balance', $data['amount'])),

        Tables\Actions\Action::make('withdraw')
          ->label('سحب نقدية')
          ->icon('heroicon-m-minus-circle')
          ->color('danger')
          ->form([
            Forms\Components\TextInput::make('amount')
              ->label('المبلغ المسحوب')
              ->numeric()
              ->required()
              ->minValue(1)
              ->rules([
                fn(CompanyFund $record): \Closure => function (string $attribute, $value, \Closure $fail) use ($record) {
                  if ($value > $record->balance) {
                    $fail("لا يمكن سحب مبلغ أكبر من الرصيد الحالي ({$record->balance} USD)");
                  }
                },
              ]),
          ])
          ->action(fn(CompanyFund $record, array $data) => $record->decrement('balance', $data['amount'])),

        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make(),
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
      'index' => Pages\ListCompanyFunds::route('/'),
      'create' => Pages\CreateCompanyFund::route('/create'),
      'edit' => Pages\EditCompanyFund::route('/{record}/edit'),
    ];
  }
}