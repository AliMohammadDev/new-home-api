<?php

namespace App\Filament\Resources;

use App\Filament\Exports\CashierSalesFatoraExporter;
use App\Filament\Resources\CashierSalesFatoraResource\Pages;
use App\Filament\Resources\CashierSalesFatoraResource\RelationManagers\ItemsRelationManager;
use App\Models\CashierSalesFatora;
use App\Models\SalesPointCashier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Actions\Exports\Enums\ExportFormat;

class CashierSalesFatoraResource extends Resource
{
  protected static ?string $model = CashierSalesFatora::class;


  protected static ?string $navigationIcon = 'heroicon-o-document-text';
  protected static ?string $navigationGroup = 'إدارة المبيعات';
  protected static ?string $navigationLabel = 'فواتير الكاشيرات';
  protected static ?string $pluralModelLabel = '  فواتير الكاشيرات';
  protected static ?string $modelLabel = 'فاتورة جديد';

  public static function form(Form $form): Form
  {
    return $form->schema([
      Forms\Components\Section::make('بيانات فاتورة المبيع')
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
      ->filters([
        Tables\Filters\TrashedFilter::make()
          ->label('حالة السجلات')
          ->falseLabel('السجلات المؤرشفة فقط')
          ->trueLabel('السجلات النشطة فقط')
          ->placeholder('الكل')
          ->native(false),
      ])
      ->defaultSort('created_at', 'DESC')
      ->actions([

        Tables\Actions\Action::make('print')
          ->label('طباعة')
          ->icon('heroicon-o-printer')
          ->color('info')
          ->url(fn($record) => route('fatora.print', ['ids' => [$record->id]]))
          ->openUrlInNewTab(),

        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make()
          ->label('أرشفة'),
        Tables\Actions\RestoreAction::make()
          ->label('استعادة'),
        Tables\Actions\ForceDeleteAction::make()
          ->label('حذف نهائي')
          ->before(function (Tables\Actions\ForceDeleteAction $action, $record) {
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
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([

          Tables\Actions\DeleteBulkAction::make()
            ->label('أرشفة المحدد'),
          Tables\Actions\RestoreBulkAction::make()
            ->label('استعادة المحدد'),
          Tables\Actions\ForceDeleteBulkAction::make()
            ->label('حذف نهائي للمحدد')
            ->before(function (Tables\Actions\ForceDeleteBulkAction $action, \Illuminate\Database\Eloquent\Collection $records) {
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

          Tables\Actions\BulkAction::make('print_selected')
            ->label('طباعة الفواتير المحددة')
            ->icon('heroicon-o-printer')
            ->color('success')
            ->action(function (\Illuminate\Database\Eloquent\Collection $records, $livewire) {
              $ids = $records->pluck('id')->toArray();
              $url = route('fatora.print', ['ids' => $ids]);

                $livewire->js("window.open('{$url}', '_blank')");
            }),
        ]),
        ExportBulkAction::make()->exporter(CashierSalesFatoraExporter::class)->formats([ExportFormat::Csv, ExportFormat::Xlsx]),
      ])
      ->headerActions([
        ExportAction::make()->exporter(CashierSalesFatoraExporter::class)
          ->formats([ExportFormat::Csv, ExportFormat::Xlsx]),
      ]);
  }

  public static function getRelations(): array
  {
    return [
      ItemsRelationManager::class,
    ];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListCashierSalesFatoras::route('/'),
      'edit' => Pages\EditCashierSalesFatora::route('/{record}/edit'),
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
