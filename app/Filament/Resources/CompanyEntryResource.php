<?php

namespace App\Filament\Resources;

use App\Filament\Exports\CompanyEntryExporter;
use App\Filament\Resources\CompanyEntryResource\Pages;
use App\Models\CompanyEntry;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Database\Eloquent\Collection;

class CompanyEntryResource extends Resource
{
  protected static ?string $model = CompanyEntry::class;
  protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
  protected static ?string $navigationGroup = 'الإدارة المالية';
  protected static ?string $navigationLabel = 'حركات الصناديق';
  protected static ?string $pluralModelLabel = 'سجل حركات الصناديق';
  protected static ?string $modelLabel = 'حركة مالية';
  protected static ?int $navigationSort = 2;

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Section::make('تفاصيل الحركة المالية')
          ->schema([
            Select::make('company_treasure_id')
              ->label('الصندوق')
              ->relationship('treasure', 'name')
              ->required()
              ->searchable()
              ->preload(),

            Select::make('user_id')
              ->label('الموظف المسؤول')
              ->relationship('user', 'name')
              ->default(auth()->id())
              ->required()
              ->searchable(),

            Select::make('trans_type')
              ->label('نوع العملية')
              ->options([
                'deposit' => 'دائن',
                'withdraw' => 'مدين',
              ])
              ->required()
              ->native(false),

            TextInput::make('amount')
              ->label('المبلغ')
              ->numeric()
              ->required()
              ->prefix('USD'),

            TextInput::make('name')
              ->label('البيان / السبب')
              ->required()
              ->columnSpanFull()
              ->placeholder('مثال: دفعة من مورد، مصاريف نثرية...'),
          ])->columns(2),
      ]);
  }
  public static function table(Table $table): Table
  {
    return $table->columns([
      TextColumn::make('created_at')
        ->label('التاريخ')
        ->searchable()
        ->dateTime('Y-m-d H:i')
        ->timezone('Asia/Riyadh'),

      TextColumn::make('debit')
        ->label('مدين')
        ->getStateUsing(
          fn($record) =>
          $record->trans_type === 'withdraw' ? $record->amount : null
        )
        ->money('USD', locale: 'en_US')
        ->summarize(
          Summarizer::make()
            ->label('إجمالي المدين')
            ->using(
              fn($query) =>
              $query->where('trans_type', 'withdraw')
                ->sum('amount')
            )
        ),

      TextColumn::make('credit')
        ->label('دائن')
        ->getStateUsing(
          fn($record) =>
          $record->trans_type === 'deposit' ? $record->amount : null
        )
        ->money('USD', locale: 'en_US')
        ->summarize(
          Summarizer::make()
            ->label('إجمالي الدائن')
            ->using(
              fn($query) =>
              $query->where('trans_type', 'deposit')
                ->sum('amount')
            )
        ),
      TextColumn::make('treasure.name')
        ->label('الصندوق')
        ->searchable()
        ->sortable(),

      TextColumn::make('user.name')
        ->label('الموظف')
        ->searchable()
        ->sortable(),

      // TextColumn::make('trans_type')
      //   ->label('نوع العملية')
      //   ->badge()
      //   ->formatStateUsing(fn(string $state): string => match ($state) {
      //     'deposit' => 'دائن',
      //     'withdraw' => 'مدين',
      //     default => $state,
      //   })
      //   ->color(fn(string $state): string => match ($state) {
      //     'deposit' => 'success',
      //     'withdraw' => 'danger',
      //     default => 'gray',
      //   })
      //   ->icon(fn(string $state): string => match ($state) {
      //     'deposit' => 'heroicon-m-arrow-trending-up',
      //     'withdraw' => 'heroicon-m-arrow-trending-down',
      //     default => 'heroicon-m-minus',
      //   }),

      // TextColumn::make('amount')
      //   ->label('المبلغ')
      //   ->money('USD', locale: 'en_US')

      TextColumn::make('name')
        ->label('البيان')
        ->searchable(),


    ])
      ->defaultSort('created_at', 'DESC')
      ->filters([
        SelectFilter::make('trans_type')
          ->label('نوع الحركة')
          ->options([
            'deposit' => 'دائن',
            'withdraw' => 'مدين',
          ]),
        SelectFilter::make('company_treasure_id')
          ->label('الصندوق')
          ->relationship('treasure', 'name'),



        TrashedFilter::make()
          ->label('حالة السجلات')
          ->falseLabel('السجلات المؤرشفة فقط')
          ->trueLabel('السجلات النشطة فقط')
          ->placeholder('الكل')
          ->native(false),
      ])
      ->actions([
        // Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make()
          ->label('أرشفة'),
        Tables\Actions\RestoreAction::make()
          ->label('استعادة'),
        Tables\Actions\ForceDeleteAction::make()
          ->label('حذف نهائي')
          ->before(function (Tables\Actions\ForceDeleteAction $action, $record) {
            if ($record->amount != 0) {
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
              $invalidRecords = $records->where('amount', '!=', 0);

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
          ->exporter(CompanyEntryExporter::class)
          ->color('success')
          ->icon('heroicon-o-arrow-down-tray')
          ->formats([ExportFormat::Csv, ExportFormat::Xlsx])
          ->visible(fn() => auth()->user()->hasRole('super_admin')),
      ])
      ->headerActions([
        ExportAction::make()
          ->exporter(CompanyEntryExporter::class)
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

  public static function getEloquentQuery(): Builder
  {
    return parent::getEloquentQuery()
      ->forActiveYear()
      ->withTrashed()
      ->with(['treasure', 'user']);
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListCompanyEntries::route('/'),
      'create' => Pages\CreateCompanyEntry::route('/create'),
      'edit' => Pages\EditCompanyEntry::route('/{record}/edit'),
    ];
  }
}
