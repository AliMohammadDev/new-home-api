<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyTreasureResource\Pages;
use App\Models\CompanyTreasure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CompanyTreasureResource extends Resource
{
  protected static ?string $model = CompanyTreasure::class;
  protected static ?string $navigationIcon = 'heroicon-o-building-library';
  protected static ?string $navigationGroup = 'الإدارة المالية';
  protected static ?string $navigationLabel = 'صناديق الشركة (الخزينة)';
  protected static ?string $pluralModelLabel = 'صناديق الشركة';
  protected static ?string $modelLabel = 'صندوق';
  protected static ?int $navigationSort = 1;

  public static function form(Form $form): Form
  {
    return $form->schema([
      Forms\Components\TextInput::make('name')
        ->label('اسم الصندوق')
        ->required(),

      Forms\Components\TextInput::make('money')
        ->label('الرصيد الحالي')
        ->numeric()
        ->default(0)
        ->required()
    ]);
  }

  public static function table(Table $table): Table
  {
    return $table->columns([
      Tables\Columns\TextColumn::make('name')->label('الصندوق')->sortable()->searchable(),
      Tables\Columns\TextColumn::make('money')
        ->label('الرصيد المتوفر')
        ->money('USD', locale: 'en_US')
        ->sortable()
        ->color(fn($state) => $state >= 0 ? 'success' : 'danger')
        ->description(fn(CompanyTreasure $record): string => $record->money < 0 ? 'رصيد سالب!' : ''),
    ])
      ->defaultSort('created_at', 'DESC')
      ->actions([
        Tables\Actions\Action::make('add_entry')
          ->label('دائن / مدين')
          ->icon('heroicon-o-arrows-right-left')
          ->color('warning')
          ->form([
            Forms\Components\Select::make('trans_type')
              ->label('نوع العملية')
              ->options(['deposit' => 'دائن', 'withdraw' => 'مدين'])
              ->required()
              ->live(),
            Forms\Components\TextInput::make('name')
              ->label('البيان / السبب')
              ->required(),
            Forms\Components\TextInput::make('amount')
              ->label('المبلغ')
              ->numeric()
              ->required()
              ->prefix('$')
              ->rules([
                fn(Forms\Get $get, $record): \Closure => function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                  if ($get('trans_type') === 'withdraw' && $value > $record->money) {
                    $fail("عذراً، الرصيد المتاح في هذا الصندوق هو ({$record->money}$) فقط.");
                  }
                },
              ])
              ->maxValue(fn(Forms\Get $get, $record) => $get('trans_type') === 'withdraw' ? $record->money : null)
              ->helperText(fn(Forms\Get $get, $record) => $get('trans_type') === 'withdraw' ? "الرصيد المتاح: {$record->money}$" : null),
          ])
          ->action(function (CompanyTreasure $record, array $data) {
            $record->entries()->create([
              'user_id' => auth()->id(),
              'trans_type' => $data['trans_type'],
              'name' => $data['name'],
              'amount' => $data['amount'],
            ]);


            \Filament\Notifications\Notification::make()
              ->title('تمت العملية بنجاح')
              ->success()
              ->send();
          }),
        Tables\Actions\EditAction::make(),
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
      'index' => Pages\ListCompanyTreasures::route('/'),
      'create' => Pages\CreateCompanyTreasure::route('/create'),
      'edit' => Pages\EditCompanyTreasure::route('/{record}/edit'),
    ];
  }
}
