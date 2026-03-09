<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanySalesTransferResource\Pages;
use App\Models\CompanySalesTransfer;
use App\Models\CompanyTreasure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CompanySalesTransferResource extends Resource
{
  protected static ?string $model = CompanySalesTransfer::class;

  protected static ?string $navigationGroup = 'نقاط البيع (POS)';

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
          ->prefix('$')
          ->rules([
            fn(Forms\Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
              if ($get('trans_type') === 'deposit') {
                $mainTreasure = CompanyTreasure::first();
                if ($mainTreasure && $value > $mainTreasure->money) {
                  $fail("عذراً، الرصيد في خزينة الشركة غير كافٍ. المتوفر حالياً: " . number_format($mainTreasure->money, 2) . " $");
                }
              }
            },
          ]),

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
          ->label('نوع العملية')
          ->formatStateUsing(fn(string $state): string => match ($state) {
            'deposit' => 'إيداع',
            'withdraw' => 'سحب',
            'withdrawal' => 'سحب',
            default => $state,
          })
          ->colors([
            'success' => 'deposit',
            'danger' => fn($state) => in_array($state, ['withdraw', 'withdrawal']),
          ])
          ->icons([
            'heroicon-m-arrow-trending-up' => 'deposit',
            'heroicon-m-arrow-trending-down' => fn($state) => in_array($state, ['withdraw', 'withdrawal']),
          ]),

        Tables\Columns\TextColumn::make('quantity')
          ->numeric()
          ->money('USD', locale: 'en_US')
          ->sortable()
          ->label('المبلغ'),

        Tables\Columns\TextColumn::make('date')
          ->date()
          ->sortable(),
      ])
      ->defaultSort('created_at', 'DESC')
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