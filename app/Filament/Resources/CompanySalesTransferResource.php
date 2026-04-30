<?php

namespace App\Filament\Resources;

use App\Filament\Exports\CompanySalesTransferExporter;
use App\Filament\Resources\CompanySalesTransferResource\Pages;
use App\Models\CompanySalesTransfer;
use App\Models\CompanyTreasure;
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
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\RestoreAction;

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
        Select::make('sales_point_id')
          ->label('نقطة البيع')
          ->relationship(
            name: 'salesPoint',
            titleAttribute: 'name',
            modifyQueryUsing: fn(Builder $query) =>
            auth()->user()->hasRole('super_admin')
            ? $query
            : $query->whereHas('managers', fn($q) => $q->where('user_id', auth()->id()))
          )
          ->required()
          ->searchable()
          ->preload()
          ->live(),

        TextInput::make('name')
          ->label('بيان العملية / المادة')
          ->placeholder('مثلاً: تحويل عهدة نقدية')
          ->maxLength(255),

        DatePicker::make('date')
          ->label('التاريخ')
          ->default(now())
          ->required(),

        TextInput::make('quantity')
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

        Textarea::make('note')
          ->label('ملاحظات إضافية')
          ->columnSpanFull(),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('salesPoint.name')
          ->label('نقطة البيع')
          ->searchable()
          ->sortable(),

        TextColumn::make('name')
          ->label('البيان')
          ->searchable()
          ->placeholder('بدون بيان'),



        TextColumn::make('quantity')
          ->numeric()
          ->money('USD', locale: 'en_US')
          ->sortable()
          ->label('المبلغ')
          ->color('success')
          ->summarize(
            Sum::make()
              ->label('الإجمالي')
              ->money('USD', locale: 'en_US')
          ),

        TextColumn::make('date')
          ->date()
          ->sortable(),
      ])
      ->defaultSort('created_at', 'DESC')
      ->filters([
        SelectFilter::make('trans_type')
          ->label('نوع الحركة')
          ->options([
            'deposit' => 'دائن',
            'withdraw' => 'مدين',
          ]),

        SelectFilter::make('sales_point_id')
          ->label('نقطة البيع')
          ->relationship('salesPoint', 'name')
          ->searchable()
          ->preload(),

        Filter::make('date')
          ->form([
            Forms\Components\DatePicker::make('from')->label('من تاريخ'),
            Forms\Components\DatePicker::make('until')->label('إلى تاريخ'),
          ])
          ->query(function (Builder $query, array $data): Builder {
            return $query
              ->when($data['from'], fn($q) => $q->whereDate('date', '>=', $data['from']))
              ->when($data['until'], fn($q) => $q->whereDate('date', '<=', $data['until']));
          })
          ->indicateUsing(function (array $data): array {
            $indicators = [];
            if ($data['from'] ?? null)
              $indicators[] = 'من: ' . $data['from'];
            if ($data['until'] ?? null)
              $indicators[] = 'إلى: ' . $data['until'];
            return $indicators;
          }),
        Tables\Filters\TrashedFilter::make()
          ->label('حالة السجلات')
          ->falseLabel('السجلات المؤرشفة فقط')
          ->trueLabel('السجلات النشطة فقط')
          ->placeholder('الكل')
          ->native(false),
      ])
      ->actions([
        EditAction::make(),
        DeleteAction::make()
          ->label('أرشفة'),
        RestoreAction::make()
          ->label('استعادة'),
        ForceDeleteAction::make()
          ->label('حذف نهائي')
          ->before(function (ForceDeleteAction $action, $record) {
            if ($record->quantity != 0) {
              Notification::make()
                ->title('غير مسموح')
                ->body('يجب تصفير المبلغ أولاً قبل الحذف النهائي.')
                ->warning()
                ->send();
              $action->halt();
            }
          }),
      ])
      ->bulkActions([
        BulkActionGroup::make([
          DeleteBulkAction::make()
            ->label('أرشفة المحدد'),
          RestoreBulkAction::make()
            ->label('استعادة المحدد'),
          ForceDeleteBulkAction::make()
            ->label('حذف نهائي للمحدد')
            ->before(function (ForceDeleteBulkAction $action, Collection $records) {
              $invalidRecords = $records->where('quantity', '!=', 0);

              if ($invalidRecords->count() > 0) {
                Notification::make()
                  ->title('لا يمكن الحذف النهائي')
                  ->body('بعض السجلات المختارة تحتوي على مبالغ غير صفرية. يجب تصفير المبالغ أولاً.')
                  ->danger()
                  ->send();
                $action->halt();
              }
            }),
        ]),
        ExportBulkAction::make()
          ->exporter(CompanySalesTransferExporter::class)
          ->color('success')
          ->icon('heroicon-o-arrow-down-tray')
          ->formats([ExportFormat::Csv, ExportFormat::Xlsx])
          ->visible(fn() => auth()->user()->hasRole('super_admin')),
      ])
      ->headerActions([
        ExportAction::make()
          ->exporter(CompanySalesTransferExporter::class)
          ->color('success')->icon('heroicon-o-arrow-down-tray')
          ->formats([ExportFormat::Csv, ExportFormat::Xlsx])
          ->visible(fn() => auth()->user()->hasRole('super_admin')),
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

  public static function getEloquentQuery(): Builder
  {

    $query = parent::getEloquentQuery()
      ->withTrashed()
      ->with(['salesPoint']);

    if (auth()->user()->hasRole('super_admin')) {
      return $query;
    }

    return $query->whereHas('salesPoint.managers', function (Builder $subQuery) {
      $subQuery->where('user_id', auth()->id());
    });
  }
}