<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashierReturnFatoraResource\Pages;
use App\Models\CashierReturnFatora;
use App\Models\SalesPointCashier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;


class CashierReturnFatoraResource extends Resource
{
  protected static ?string $model = CashierReturnFatora::class;
  protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
  protected static ?string $navigationGroup = 'إدارة المبيعات';
  protected static ?string $navigationLabel = 'فواتير مرتجعات الكاشير';
  protected static ?string $modelLabel = 'فاتورة مرتجع';
  protected static ?string $pluralModelLabel = 'فواتير المرتجعات';

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make('تفاصيل المرتجع')
          ->schema([
            Forms\Components\Select::make('sales_point_cashier_id')
              ->label('الكاشير المسؤول')
              ->relationship('cashier', 'id')
              ->getOptionLabelFromRecordUsing(fn($record) => $record->user?->name . " - " . $record->salesPoint?->name)
              ->searchable()
              ->preload()
              ->required(),

            Forms\Components\DatePicker::make('date')
              ->label('تاريخ الفاتورة')
              ->default(now())
              ->required(),

            Forms\Components\TextInput::make('full_price')
              ->label('إجمالي المبلغ')
              ->numeric()
              ->disabled()
              ->prefix('USD')
              ->required(),
          ])->columns(2),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table->columns([
      Tables\Columns\TextColumn::make('id')->label('رقم الفاتورة')->sortable(),
      Tables\Columns\TextColumn::make('cashier.user.name')->label('اسم الكاشير')->searchable(),
      Tables\Columns\TextColumn::make('cashier.salesPoint.name')->label('نقطة البيع')->badge(),
      Tables\Columns\TextColumn::make('date')->label('التاريخ')->date()->sortable(),
      Tables\Columns\TextColumn::make('full_price')
        ->label('الإجمالي')
        ->money('USD', locale: 'en_US')
    ])
      ->defaultSort('created_at', 'DESC')
      ->filters([
        //
      ])
      ->actions([

        Tables\Actions\Action::make('print')
          ->label('طباعة')
          ->icon('heroicon-o-printer')
          ->color('info')
          ->url(fn($record) => route('fatora.print', ['ids' => [$record->id]]))
          ->openUrlInNewTab(),

        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make()
          ->modalHeading('حذف فاتورة المرتجع')
          ->modalDescription('سيتم خصم الكميات المرتجعة من المستودع وإعادة المبالغ لعهدة الكاشير، هل أنت متأكد؟')
      ])
      ->bulkActions([

        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
          Tables\Actions\BulkAction::make('print_selected')
            ->label('طباعة الفواتير المحددة')
            ->icon('heroicon-o-printer')
            ->color('success')
            ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
              return redirect()->route('fatora.print', [
                'ids' => $records->pluck('id')->toArray()
              ]);
            }),
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
      'index' => Pages\ListCashierReturnFatoras::route('/'),
      'edit' => Pages\EditCashierReturnFatora::route('/{record}/edit'),
    ];
  }
  public static function canCreate(): bool
  {
    return false;
  }

  public static function getEloquentQuery(): Builder
  {
    $query = parent::getEloquentQuery();

    if (auth()->user()->hasRole('super_admin')) {
      return $query;
    }

    $cashierId = SalesPointCashier::where('user_id', auth()->id())->value('id');

    if (!$cashierId) {
      return $query->whereRaw('1 = 0');
    }

    return $query->where('sales_point_cashier_id', $cashierId);
  }


}