<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\User;
use Filament\Tables\Columns\ToggleColumn;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CustomerResource extends Resource
{
  protected static ?string $model = User::class;

  protected static ?string $navigationIcon = 'heroicon-o-user-group';
  protected static ?string $navigationLabel = 'العملاء';
  protected static ?string $pluralModelLabel = 'العملاء';
  protected static ?string $modelLabel = 'عميل';
  protected static ?string $navigationGroup = 'إدارة المستخدمين';
  protected static ?int $navigationSort = 1;

  public static function getEloquentQuery(): Builder
  {
    return parent::getEloquentQuery()
      ->role('customer');
  }


  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('id')
          ->label('ID')
          ->sortable()
          ->searchable(),

        TextColumn::make('name')
          ->label('الاسم')
          ->sortable()
          ->searchable(),

        TextColumn::make('email')
          ->label('البريد الإلكتروني')
          ->sortable()
          ->searchable()
          ->icon('heroicon-m-envelope')
          ->iconColor('gray'),

        ToggleColumn::make('is_active')
          ->label('الحالة')
          ->onIcon('heroicon-m-check-circle')
          ->offIcon('heroicon-m-x-circle')
          ->onColor('success')
          ->offColor('danger'),

        TextColumn::make('roles.display_name')
          ->label('نوع الحساب')
          ->badge()
          ->formatStateUsing(function ($state, $record) {
            $locale = app()->getLocale();
            return $state[$locale] ?? $state['ar'] ?? $record->name;
          })
          ->color('success'),

        TextColumn::make('created_at')
          ->label('تاريخ التسجيل')
          ->dateTime('Y-m-d')
          ->sortable()
          ->searchable()
          ->color('gray'),
      ])
      ->defaultSort('created_at', 'desc')
      ->filters([
        Tables\Filters\TernaryFilter::make('is_active')
          ->label('حالة الحساب')
          ->placeholder('الكل')
          ->trueLabel('نشط')
          ->falseLabel('معطل'),
      ])
      ->actions([
        Tables\Actions\ViewAction::make()->label('عرض'),

      ])
      ->bulkActions([

      ]);
  }
  public static function getPages(): array
  {
    return [
      'index' => Pages\ListCustomers::route('/'),
      'edit' => Pages\EditCustomer::route('/{record}/edit'),
    ];
  }
  public static function canCreate(): bool
  {
    return false;
  }
}
