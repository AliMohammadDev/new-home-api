<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesPointCashierTransResource\Pages;
use App\Filament\Resources\SalesPointCashierTransResource\RelationManagers;
use App\Models\SalesPointCashierTrans;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SalesPointCashierTransResource extends Resource
{
  protected static ?string $model = SalesPointCashierTrans::class;
  protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
  protected static ?string $navigationGroup = 'إدارة المبيعات';
  protected static ?string $navigationLabel = 'تحويلات الكاشيرات';
  protected static ?string $pluralModelLabel = '  تحويلات الكاشيرات';
  protected static ?string $modelLabel = 'تحويل جديد';

  public static function form(Form $form): Form
  {
    return $form->schema([
      Forms\Components\Section::make('تفاصيل المناقلة')
        ->schema([
          Forms\Components\Select::make('sales_point_id')
            ->label('نقطة البيع')
            ->relationship('salesPoint', 'name')
            ->required()
            ->searchable()
            ->preload()
            ->live(),

          Forms\Components\Select::make('sales_point_manager_id')
            ->label('المدير المسؤول')
            ->relationship('manager', 'id', function ($query) {
              return $query->with('user');
            })
            ->getOptionLabelFromRecordUsing(fn($record) => $record->user?->name)
            ->searchable()
            ->preload()
            ->required(),

          Forms\Components\Select::make('sales_point_cashier_id')
            ->label('الموظف المستلم (الكاشير)')
            ->relationship('cashier', 'id', function ($query) {
              return $query->with('user');
            })
            ->getOptionLabelFromRecordUsing(fn($record) => $record->user?->name)
            ->searchable()
            ->preload()
            ->required(),

          Forms\Components\TextInput::make('name')
            ->label('بيان العملية / المادة')
            ->placeholder('مثلاً: تحويل عهدة نقدية')
            ->required(),

          Forms\Components\DatePicker::make('date')
            ->label('التاريخ')
            ->default(now())
            ->required(),

          Forms\Components\Select::make('trans_type')
            ->label('نوع العملية')
            ->options([
              'deposit' => 'إيداع',
              'withdrawal' => 'سحب',
            ])->required(),

          Forms\Components\TextInput::make('amount')
            ->label('الكمية / المبلغ')
            ->numeric()
            ->required(),

          Forms\Components\Textarea::make('note')
            ->label('ملاحظات إضافية')
            ->columnSpanFull(),
        ])->columns(2),
    ]);
  }

  public static function table(Table $table): Table
  {
    return $table->columns([
      Tables\Columns\TextColumn::make('date')
        ->label('التاريخ')
        ->date()
        ->sortable(),

      Tables\Columns\TextColumn::make('salesPoint.name')
        ->label('نقطة البيع')
        ->badge()
        ->color('gray'),

      Tables\Columns\TextColumn::make('manager.user.name')
        ->label('المدير المسلم')
        ->searchable(),

      Tables\Columns\TextColumn::make('cashier.user.name')
        ->label('المستلم')
        ->searchable(),

      Tables\Columns\TextColumn::make('trans_type')
        ->label('النوع')
        ->badge()
        ->color(fn(string $state): string => match ($state) {
          'deposit' => 'success',
          'withdrawal' => 'danger',
          'transfer' => 'warning',
        })
        ->formatStateUsing(fn(string $state): string => match ($state) {
          'deposit' => 'إيداع',
          'withdrawal' => 'سحب',
        }),

      Tables\Columns\TextColumn::make('amount')
        ->label('الكمية')
        ->money('USD', locale: 'en_US')
        ->sortable(),

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
      'index' => Pages\ListSalesPointCashierTrans::route('/'),
      'create' => Pages\CreateSalesPointCashierTrans::route('/create'),
      'edit' => Pages\EditSalesPointCashierTrans::route('/{record}/edit'),
    ];
  }
}
