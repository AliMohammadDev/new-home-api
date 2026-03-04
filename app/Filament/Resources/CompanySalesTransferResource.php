<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanySalesTransferResource\Pages;
use App\Models\CompanySalesTransfer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CompanySalesTransferResource extends Resource
{
  protected static ?string $model = CompanySalesTransfer::class;

  protected static ?string $navigationGroup = 'إدارة المبيعات';
  protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
  protected static ?string $navigationLabel = 'تحويلات نقاط البيع';
  protected static ?string $modelLabel = 'تحويل مالي';
  protected static ?string $pluralModelLabel = 'التحويلات المالية';



  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Select::make('sales_point_id')
          ->label('نقطة البيع')
          ->relationship('salesPoint', 'name')
          ->required()
          ->searchable()
          ->preload()
          ->live(),

        Forms\Components\Select::make('trans_type')
          ->label('نوع العملية')
          ->options([
            'deposit' => 'إيداع',
            'withdraw' => 'سحب',
          ])->required(),


        Forms\Components\TextInput::make('name')
          ->label('بيان العملية / المادة')
          ->placeholder('مثلاً: تحويل عهدة نقدية')
          ->maxLength(255),

        Forms\Components\DatePicker::make('date')
          ->label('التاريخ')
          ->default(now())
          ->required(),

        Forms\Components\TextInput::make('quantity')
          ->label('الكمية / المبلغ')
          ->numeric()
          ->required()
          ->prefix('$'),

        Forms\Components\Textarea::make('note')
          ->label('ملاحظات إضافية')
          ->columnSpanFull(),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('salesPoint.name')
          ->label('نقطة البيع')
          ->sortable(),

        Tables\Columns\BadgeColumn::make('trans_type')
          ->colors([
            'success' => 'deposit',
            'danger' => 'withdraw',
          ])
          ->label('نوع العملية'),

        Tables\Columns\TextColumn::make('quantity')
          ->numeric()
          ->money('USD', locale: 'en_US')
          ->sortable()
          ->label('المبلغ'),

        Tables\Columns\TextColumn::make('date')
          ->date()
          ->sortable(),
      ])
      ->filters([
        Tables\Filters\SelectFilter::make('trans_type')
          ->options([
            'deposit' => 'إيداع',
            'withdraw' => 'سحب',
          ]),
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
      'index' => Pages\ListCompanySalesTransfers::route('/'),
      'create' => Pages\CreateCompanySalesTransfer::route('/create'),
      'edit' => Pages\EditCompanySalesTransfer::route('/{record}/edit'),
    ];
  }
}