<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesPointCashierTransResource\Pages;
use App\Filament\Resources\SalesPointCashierTransResource\RelationManagers;
use App\Models\SalesPoint;
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
            ->options(function (callable $get) {
              $salesPointId = $get('sales_point_id');

              if (!$salesPointId) {
                return [];
              }

              return \App\Models\SalesPointManager::query()
                ->where('sales_point_id', $salesPointId)
                ->with('user')
                ->get()
                ->pluck('user.name', 'id');
            })
            ->searchable()
            ->required()
            ->disabled(fn(callable $get) => !$get('sales_point_id')),

          Forms\Components\Select::make('sales_point_cashier_id')
            ->label('الموظف المستلم (الكاشير)')
            ->options(function (callable $get) {
              $salesPointId = $get('sales_point_id');

              if (!$salesPointId) {
                return [];
              }

              return \App\Models\SalesPointCashier::query()
                ->where('sales_point_id', $salesPointId)
                ->with('user')
                ->get()
                ->pluck('user.name', 'id');
            })
            ->searchable()
            ->required()
            ->disabled(fn(callable $get) => !$get('sales_point_id')),

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
            ->label('المبلغ')
            ->numeric()
            ->required()
            ->rules([
              fn(Forms\Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                if ($get('trans_type') === 'deposit') {
                  $salesPoint = SalesPoint::find($get('sales_point_id'));
                  if ($salesPoint && $value > $salesPoint->amount) {
                    $fail("رصيد نقطة البيع غير كافٍ. الرصيد الحالي: " . $salesPoint->amount);
                  }
                }
              },
            ]),

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
        ->label('نوع العملية')
        ->badge()
        ->color(fn(string $state): string => match ($state) {
          'deposit' => 'success',
          'withdrawal', 'withdraw' => 'danger',
          'transfer' => 'warning',
          default => 'gray',
        })
        ->formatStateUsing(fn(string $state): string => match ($state) {
          'deposit' => 'إيداع',
          'withdrawal', 'withdraw' => 'سحب',
          'transfer' => 'تحويل',
          default => $state,
        })
        ->icons([
          'heroicon-m-arrow-trending-up' => 'deposit',
          'heroicon-m-arrow-trending-down' => fn($state) => in_array($state, ['withdrawal', 'withdraw']),
          'heroicon-m-arrows-right-left' => 'transfer',
        ]),

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