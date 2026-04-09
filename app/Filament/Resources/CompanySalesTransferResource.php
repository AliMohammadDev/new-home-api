<?php

namespace App\Filament\Resources;

use App\Filament\Exports\CompanySalesTransferExporter;
use App\Filament\Resources\CompanySalesTransferResource\Pages;
use App\Models\CompanySalesTransfer;
use App\Models\CompanyTreasure;
use Faker\Provider\Company;
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

        Forms\Components\Select::make('trans_type')
          ->label('نوع العملية')
          ->options([
            'deposit' => 'دائن',
            'withdraw' => 'مدين',
          ])
          ->required(),


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
          ->searchable()
          ->sortable(),

        Tables\Columns\TextColumn::make('name')
          ->label('البيان')
          ->searchable()
          ->placeholder('بدون بيان'),

        Tables\Columns\TextColumn::make('trans_type')
          ->label('نوع العملية')
          ->badge()
          ->formatStateUsing(fn(string $state): string => match ($state) {
            'deposit' => 'دائن',
            'withdraw' => 'مدين',
            default => $state,
          })
          ->color(fn(string $state): string => match ($state) {
            'deposit' => 'success',
            'withdraw' => 'danger',
            default => 'gray',
          })
          ->icon(fn(string $state): string => match ($state) {
            'deposit' => 'heroicon-m-arrow-trending-up',
            'withdraw' => 'heroicon-m-arrow-trending-down',
            default => 'heroicon-m-minus',
          }),


        Tables\Columns\TextColumn::make('quantity')
          ->numeric()
          ->money('USD', locale: 'en_US')
          ->sortable()
          ->label('المبلغ')
          ->summarize(
            Tables\Columns\Summarizers\Sum::make()
              ->label('الإجمالي')
              ->money('USD', locale: 'en_US')
          ),

        Tables\Columns\TextColumn::make('date')
          ->date()
          ->sortable(),
      ])
      ->defaultSort('created_at', 'DESC')
      ->filters([
        Tables\Filters\SelectFilter::make('trans_type')
          ->label('نوع الحركة')
          ->options([
            'deposit' => 'دائن',
            'withdraw' => 'مدين',
          ]),

        Tables\Filters\SelectFilter::make('sales_point_id')
          ->label('نقطة البيع')
          ->relationship('salesPoint', 'name')
          ->searchable()
          ->preload(),

        Tables\Filters\Filter::make('date')
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
        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make()
          ->label('أرشفة'),
        Tables\Actions\RestoreAction::make()
          ->label('استعادة'),
        Tables\Actions\ForceDeleteAction::make()
          ->label('حذف نهائي')
          ->before(function (Tables\Actions\ForceDeleteAction $action, $record) {
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
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make()
            ->label('أرشفة المحدد'),
          Tables\Actions\RestoreBulkAction::make()
            ->label('استعادة المحدد'),
          Tables\Actions\ForceDeleteBulkAction::make()
            ->label('حذف نهائي للمحدد')
            ->before(function (Tables\Actions\ForceDeleteBulkAction $action, \Illuminate\Database\Eloquent\Collection $records) {
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
          ExportBulkAction::make()->exporter(CompanySalesTransferExporter::class)->formats([ExportFormat::Csv, ExportFormat::Xlsx]),
      ])
      ->headerActions([
        ExportAction::make()->exporter(CompanySalesTransferExporter::class)
        ->formats([ExportFormat::Csv, ExportFormat::Xlsx]),
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
