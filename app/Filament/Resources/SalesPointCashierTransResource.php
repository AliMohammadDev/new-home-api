<?php

namespace App\Filament\Resources;

use App\Filament\Exports\SalesPointCashierTransExporter;
use App\Filament\Resources\SalesPointCashierTransResource\Pages;
use App\Models\SalesPoint;
use App\Models\SalesPointCashier;
use App\Models\SalesPointCashierTrans;
use App\Models\SalesPointManager;
use Filament\Forms;
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
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Collection;

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
      Section::make('تفاصيل المناقلة')
        ->schema([
          Select::make('sales_point_id')
            ->label('نقطة البيع')
            ->relationship(
              name: 'salesPoint',
              titleAttribute: 'name',
              modifyQueryUsing: function (Builder $query) {
                if (auth()->user()->hasRole('super_admin'))
                  return $query;
                return $query->whereHas('managers', fn($q) => $q->where('user_id', auth()->id()));
              }
            )
            ->required()
            ->searchable()
            ->preload()
            ->live()
            ->afterStateUpdated(function (Forms\Set $set, $state) {
              $set('sales_point_cashier_id', null);
              if (!auth()->user()->hasRole('super_admin') && $state) {
                $managerId = SalesPointManager::where('user_id', auth()->id())
                  ->where('sales_point_id', $state)
                  ->value('id');
                $set('sales_point_manager_id', $managerId);
              } else {
                $set('sales_point_manager_id', null);
              }
            }),

          Select::make('sales_point_manager_id')
            ->label('المدير المسؤول')
            ->options(function (Forms\Get $get) {
              $salesPointId = $get('sales_point_id');
              if (!$salesPointId)
                return [];

              $query = SalesPointManager::query()->where('sales_point_id', $salesPointId);

              if (!auth()->user()->hasRole('super_admin')) {
                $query->where('user_id', auth()->id());
              }

              return $query->with('user')->get()->pluck('user.name', 'id');
            })
            ->required()
            ->searchable()
            ->live()
            ->disabled(fn() => !auth()->user()->hasRole('super_admin'))
            ->dehydrated()
            ->helperText(fn() => !auth()->user()->hasRole('super_admin') ? 'يتم تحديد المدير تلقائياً بناءً على حسابك.' : ''),

          Select::make('sales_point_cashier_id')
            ->label('الموظف المستلم (الكاشير)')
            ->key(fn(Forms\Get $get) => 'cashier_' . $get('sales_point_id'))
            ->options(function (Forms\Get $get) {
              $salesPointId = $get('sales_point_id');
              if (!$salesPointId)
                return [];
              return SalesPointCashier::query()
                ->where('sales_point_id', $salesPointId)
                ->with('user')
                ->get()
                ->pluck('user.name', 'id');
            })
            ->searchable()
            ->required()
            ->disabled(fn(Forms\Get $get) => !$get('sales_point_id'))
            ->live()
            ->afterStateUpdated(function ($state, Forms\Set $set) {
              if (!$state) {
                $set('current_cashier_balance', 0);
                return;
              }

              $balance = SalesPointCashier::where('id', $state)->value('daily_limit') ?? 0;

              $set('current_cashier_balance', number_format($balance, 2));
            }),



          TextInput::make('name')
            ->label('بيان العملية / المادة')
            ->placeholder('مثلاً: تحويل عهدة نقدية')
            ->required(),

          DatePicker::make('date')
            ->label('التاريخ')
            ->default(now())
            ->required(),

          Select::make('trans_type')
            ->label('نوع العملية')
            ->options([
              'deposit' => 'دائن',
              'withdraw' => 'مدين',
            ])
            ->required(),

          TextInput::make('amount')
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

          TextInput::make('waste')
            ->label('كمية الهدر (إن وجد)')
            ->numeric()
            ->default(0)
            ->nullable()
            ->prefixIcon('heroicon-m-variable')
            ->helperText('أدخل كمية الهدر أو التالف في هذه العملية إن وجدت.'),


          TextInput::make('current_cashier_balance')
            ->label('رصيد الصندوق الحالي')
            ->prefix('$')
            ->readonly()
            ->numeric()
            ->placeholder('اختر كاشير لرؤية الرصيد')
            ->extraInputAttributes(['style' => 'font-weight: bold; color: #10b981;'])
            ->dehydrated(false),

          Textarea::make('note')
            ->label('ملاحظات إضافية')
            ->columnSpanFull(),
        ])->columns(2),
    ]);
  }

  public static function table(Table $table): Table
  {
    return $table->columns([
      TextColumn::make('date')
        ->label('التاريخ')
        ->date()
        ->dateTime('Y-m-d H:i')
        ->timezone('Asia/Riyadh')
        ->sortable(),

      TextColumn::make('salesPoint.name')
        ->label('نقطة البيع')
        ->badge()
        ->color('gray'),

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
            ->using(function ($query) {
              return $query->where('trans_type', 'withdraw')->sum('amount');
            })
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
            ->using(function ($query) {
              return $query->where('trans_type', 'deposit')->sum('amount');
            })
        ),

      TextColumn::make('name')
        ->label('البيان / المادة')
        ->searchable(),

      TextColumn::make('manager.user.name')
        ->label('المدير المسلم')
        ->searchable(),

      TextColumn::make('cashier.user.name')
        ->label('المستلم')
        ->searchable(),



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
      //   ->label('الكمية')
      //   ->money('USD', locale: 'en_US')
      //   ->sortable()
      //   ->summarize([
      //     Sum::make()
      //       ->label('الإجمالي')
      //       ->money('USD', locale: 'en_US'),
      //   ]),

      TextColumn::make('waste')
        ->label('الهدر')
        ->numeric(decimalPlaces: 2, locale: 'en')
        ->color('warning')
        ->sortable()
        ->placeholder('0.00')
        ->summarize([
          Sum::make()
            ->label('إجمالي الهدر')
            ->numeric(locale: 'en'),
        ]),
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
          ->relationship('salesPoint', 'name'),

        Filter::make('date')
          ->form([
            DatePicker::make('from')->label('من تاريخ'),
            DatePicker::make('until')->label('إلى تاريخ'),
          ])
          ->query(function (Builder $query, array $data): Builder {
            return $query
              ->when($data['from'], fn($q) => $q->whereDate('date', '>=', $data['from']))
              ->when($data['until'], fn($q) => $q->whereDate('date', '<=', $data['until']));
          }),

        TrashedFilter::make()
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
          ->exporter(SalesPointCashierTransExporter::class)
          ->color('success')->icon('heroicon-o-arrow-down-tray')
          ->formats([ExportFormat::Csv, ExportFormat::Xlsx])
          ->visible(fn() => auth()->user()->hasRole('super_admin')),
      ])
      ->headerActions([
        ExportAction::make()->exporter(SalesPointCashierTransExporter::class)
          ->color('success')
          ->icon('heroicon-o-arrow-down-tray')
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
      'index' => Pages\ListSalesPointCashierTrans::route('/'),
      'create' => Pages\CreateSalesPointCashierTrans::route('/create'),
      'edit' => Pages\EditSalesPointCashierTrans::route('/{record}/edit'),
    ];
  }

  public static function getEloquentQuery(): Builder
  {
    $user = auth()->user();
    $query = parent::getEloquentQuery()
      ->forActiveYear()
      ->withTrashed()
      ->with([
        'salesPoint',
        'manager.user',
        'cashier.user'
      ]);

    if ($user->hasRole('super_admin')) {
      return $query;
    }

    $managerIds = SalesPointManager::where('user_id', $user->id)->pluck('id')->toArray();
    $cashierIds = SalesPointCashier::where('user_id', $user->id)->pluck('id')->toArray();

    return $query->where(function (Builder $subQuery) use ($managerIds, $cashierIds) {
      if (!empty($managerIds)) {
        $subQuery->orWhereIn('sales_point_manager_id', $managerIds);
      }

      if (!empty($cashierIds)) {
        $subQuery->orWhereIn('sales_point_cashier_id', $cashierIds);
      }
    });
  }
}
