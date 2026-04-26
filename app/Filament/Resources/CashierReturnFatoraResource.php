<?php

namespace App\Filament\Resources;

use App\Filament\Exports\CashierReturnFatoraExporter;
use App\Filament\Resources\CashierReturnFatoraResource\Pages;
use App\Models\CashierReturnFatora;
use App\Models\SalesPointCashier;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;

use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\Action;


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
        Section::make('تفاصيل المرتجع')
          ->schema([
            Select::make('sales_point_cashier_id')
              ->label('الكاشير المسؤول')
              ->relationship('cashier', 'id')
              ->getOptionLabelFromRecordUsing(fn($record) => $record->user?->name . " - " . $record->salesPoint?->name)
              ->searchable()
              ->preload()
              ->required(),

            DatePicker::make('date')
              ->label('تاريخ الفاتورة')
              ->default(now())
              ->required(),

            TextInput::make('full_price')
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
      TextColumn::make('id')
        ->label('رقم الفاتورة')
        ->sortable(),
      TextColumn::make('cashier.user.name')
        ->label('اسم الكاشير')
        ->searchable(),
      TextColumn::make('cashier.salesPoint.name')
        ->label('نقطة البيع')
        ->badge(),
      TextColumn::make('date')
        ->label('التاريخ')
        ->date()
        ->sortable(),
      TextColumn::make('full_price')
        ->label('الإجمالي')
        ->money('USD', locale: 'en_US')
    ])
      ->defaultSort('created_at', 'DESC')
      ->filters([
        TrashedFilter::make()
          ->label('حالة السجلات')
          ->falseLabel('السجلات المؤرشفة فقط')
          ->trueLabel('السجلات النشطة فقط')
          ->placeholder('الكل')
          ->native(false),
      ])
      ->actions([
        Action::make('print')
          ->label('طباعة')
          ->icon('heroicon-o-printer')
          ->color('info')
          ->url(fn($record) => route('fatora.print', ['ids' => [$record->id]]))
          ->openUrlInNewTab(),

        EditAction::make(),
        DeleteAction::make()
          ->label('أرشفة'),
        RestoreAction::make()
          ->label('استعادة'),
        ForceDeleteAction::make()
          ->label('حذف نهائي')
          ->before(function (ForceDeleteAction $action, $record) {
            if (round((float) $record->full_price, 2) > 0) {
              Notification::make()
                ->title('غير مسموح')
                ->body('لا يمكن حذف الفاتورة نهائياً لأن رصيدها (' . $record->full_price . ') لم يتم تصفيره.')
                ->danger()
                ->send();
              $action->halt();
            }
          }),
      ])
      ->headerActions([
        ExportAction::make()
          ->exporter(CashierReturnFatoraExporter::class)
          ->color('success')
          ->icon('heroicon-o-arrow-down-tray')
          ->formats([ExportFormat::Csv, ExportFormat::Xlsx])
          ->visible(fn() => auth()->user()->hasRole('super_admin')),
      ])
      ->bulkActions([
        BulkActionGroup::make([
          ExportBulkAction::make()
            ->exporter(CashierReturnFatoraExporter::class)
            ->color('success')
            ->icon('heroicon-o-arrow-down-tray')
            ->formats([ExportFormat::Csv, ExportFormat::Xlsx])
            ->visible(fn() => auth()->user()->hasRole('super_admin')),

          DeleteBulkAction::make()
            ->label('أرشفة المحدد'),
          RestoreBulkAction::make()
            ->label('استعادة المحدد'),
          ForceDeleteBulkAction::make()
            ->label('حذف نهائي للمحدد')
            ->before(function (ForceDeleteBulkAction $action, \Illuminate\Database\Eloquent\Collection $records) {
              $hasBalance = $records->contains(fn($record) => round((float) $record->full_price, 2) > 0);

              if ($hasBalance) {
                Notification::make()
                  ->title('إجراء محظور')
                  ->body('بعض الفواتير المختارة تحتوي على مبالغ. يجب تصفير كافة الفواتير قبل الحذف النهائي.')
                  ->danger()
                  ->send();

                $action->halt();
              }
            }),


          BulkAction::make('print_selected')
            ->label('طباعة الفواتير المحددة')
            ->icon('heroicon-o-printer')
            ->color('success')
            ->action(function (\Illuminate\Database\Eloquent\Collection $records, $livewire) {
              $ids = $records->pluck('id')->toArray();
              $url = route('fatora.print', ['ids' => $ids]);

              $livewire->js("window.open('{$url}', '_blank')");
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
    $query = parent::getEloquentQuery()
      ->withTrashed()
      ->with(['cashier.user', 'cashier.salesPoint']);

    $user = auth()->user();

    if ($user->hasRole('super_admin')) {
      return $query;
    }

    if ($user->hasRole('sales_point_manager')) {
      return $query->whereHas('cashier.salesPoint.managers', function (Builder $subQuery) use ($user) {
        $subQuery->where('user_id', $user->id);
      });
    }

    if ($user->hasRole('sales_point_cashier')) {
      $cashierId = SalesPointCashier::where('user_id', $user->id)->value('id');

      if (!$cashierId) {
        return $query->whereRaw('1 = 0');
      }

      return $query->where('sales_point_cashier_id', $cashierId);
    }

    return $query->whereRaw('1 = 0');
  }


}