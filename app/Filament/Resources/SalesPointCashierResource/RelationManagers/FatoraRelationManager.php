<?php

namespace App\Filament\Resources\SalesPointCashierResource\RelationManagers;

use App\Filament\Resources\CashierSalesFatoraResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class FatoraRelationManager extends RelationManager
{
  protected static string $relationship = 'fatora';
  protected static ?string $title = 'سجل فواتير المبيعات';
  protected static ?string $modelLabel = 'فاتورة';

  public function table(Table $table): Table
  {
    return $table
      ->recordTitleAttribute('id')
      ->recordUrl(
        fn(Model $record): string => CashierSalesFatoraResource::getUrl('edit', ['record' => $record])
      )
      ->columns([
        Tables\Columns\TextColumn::make('id')
          ->label('رقم الفاتورة')
          ->searchable()
          ->sortable()
          ->weight('bold'),

        Tables\Columns\TextColumn::make('date')
          ->label('التاريخ')
          ->dateTime('Y-m-d H:i')
          ->sortable(),

        Tables\Columns\TextColumn::make('items_count')
          ->label('عدد المواد')
          ->counts('items')
          ->badge()
          ->color('gray'),

        Tables\Columns\TextColumn::make('full_price')
          ->label('إجمالي الفاتورة')
          ->money('USD', locale: 'en_US')
          ->color('success')
          ->weight('bold')
          ->summarize(
            Tables\Columns\Summarizers\Sum::make()
              ->label('المجموع الكلي')
              ->money('USD', locale: 'en_US')
          ),
      ])
      ->filters([
        Tables\Filters\Filter::make('date')
          ->form([
            Forms\Components\DatePicker::make('from')->label('من تاريخ'),
            Forms\Components\DatePicker::make('until')->label('إلى تاريخ'),
          ])
          ->query(
            fn($query, array $data) => $query
              ->when($data['from'], fn($q) => $q->whereDate('date', '>=', $data['from']))
              ->when($data['until'], fn($q) => $q->whereDate('date', '<=', $data['until']))
          )
      ])
      ->headerActions([
      ])
      ->actions([
        Tables\Actions\Action::make('print')
          ->label('طباعة')
          ->icon('heroicon-o-printer')
          ->color('info')
          ->url(fn($record) => route('fatora.print', ['ids' => [$record->id]]))
          ->openUrlInNewTab(),

        Tables\Actions\EditAction::make()
          ->label('التفاصيل')
          ->url(fn(Model $record): string => CashierSalesFatoraResource::getUrl('edit', ['record' => $record])),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\BulkAction::make('print_selected')
            ->label('طباعة المحدد')
            ->icon('heroicon-o-printer')
            ->action(fn($records) => redirect()->route('fatora.print', ['ids' => $records->pluck('id')->toArray()])),
        ]),
      ]);
  }
}
