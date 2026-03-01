<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesPointCashierResource\Pages;
use App\Filament\Resources\SalesPointCashierResource\RelationManagers;
use App\Models\SalesPointCashier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SalesPointCashierResource extends Resource
{
  protected static ?string $model = SalesPointCashier::class;
  protected static ?string $navigationGroup = 'إدارة المبيعات';
  protected static ?string $navigationLabel = 'الكاشيرات';
  protected static ?string $navigationIcon = 'heroicon-o-calculator';

  protected static ?string $pluralModelLabel = ' الكاشيرات';
  protected static ?string $modelLabel = 'كاشير جديد';


  public static function form(Form $form): Form
  {
    return $form->schema([
      Forms\Components\Section::make('بيانات الكاشير الأساسية')
        ->schema([
          Forms\Components\Select::make('sales_point_id')
            ->label('نقطة البيع')
            ->relationship('salesPoint', 'name')
            ->searchable()
            ->preload()
            ->required(),

          Forms\Components\Select::make('user_id')
            ->label('المستخدم (الكاشير)')
            ->relationship('user', 'name')
            ->searchable()
            ->preload()
            ->required(),

          Forms\Components\Select::make('shift_type')
            ->label('نوع الوردية')
            ->options([
              'morning' => 'صباحية',
              'evening' => 'مسائية',
              'night' => 'ليلية',
              'full_time' => 'دوام كامل',
            ])
            ->native(false)
            ->required(),

          Forms\Components\TextInput::make('daily_limit')
            ->label('حد الصندوق اليومي')
            ->numeric()
            ->prefix('SYP')
            ->default(0),

          Forms\Components\Toggle::make('is_active')
            ->label('الحالة التشغيلية')
            ->default(true)
            ->onColor('success'),
        ])->columns(2),
    ]);
  }
  public static function table(Table $table): Table
  {

    return $table
      ->columns([
        Tables\Columns\TextColumn::make('user.name')
          ->label('الكاشير')
          ->sortable()
          ->searchable(),

        Tables\Columns\TextColumn::make('salesPoint.name')
          ->label('نقطة البيع')
          ->badge(),

        Tables\Columns\TextColumn::make('shift_type')
          ->label('الوردية')
          ->badge()
          ->color(fn(string $state): string => match ($state) {
            'morning' => 'warning',
            'evening' => 'info',
            'night' => 'primary',
            'full_time' => 'success',
            default => 'gray',
          })
          ->formatStateUsing(fn(string $state): string => match ($state) {
            'morning' => 'صباحية',
            'evening' => 'مسائية',
            'night' => 'ليلية',
            'full_time' => 'دوام كامل',
            default => $state,
          }),

        Tables\Columns\TextColumn::make('daily_limit')
          ->label('حد الصندوق')
          ->money('SYP'),
      ])
      ->filters([
        //
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make(),
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
      'index' => Pages\ListSalesPointCashiers::route('/'),
      'create' => Pages\CreateSalesPointCashier::route('/create'),
      'edit' => Pages\EditSalesPointCashier::route('/{record}/edit'),
    ];
  }
}
