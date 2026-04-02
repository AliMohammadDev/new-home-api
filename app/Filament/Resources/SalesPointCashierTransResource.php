<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesPointCashierTransResource\Pages;
use App\Models\SalesPoint;
use App\Models\SalesPointCashier;
use App\Models\SalesPointCashierTrans;
use App\Models\SalesPointManager;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
      Forms\Components\Section::make('تفاصيل المناقلة')
        ->schema([
          Forms\Components\Select::make('sales_point_id')
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

          Forms\Components\Select::make('sales_point_manager_id')
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

          Forms\Components\Select::make('sales_point_cashier_id')
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



          Forms\Components\TextInput::make('name')
            ->label('بيان العملية / المادة')
            ->placeholder('مثلاً: تحويل عهدة نقدية')
            ->required(),

          Forms\Components\DatePicker::make('date')
            ->label('التاريخ')
            ->default(now())
            ->required(),

          Forms\Components\Select::make('trans_type')
            ->label('نوع العملية')
            ->options([
              'deposit' => 'دائن',
              'withdraw' => 'مدين',
            ])
            ->required(),

          Forms\Components\TextInput::make('amount')
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


          Forms\Components\TextInput::make('current_cashier_balance')
            ->label('رصيد الصندوق الحالي')
            ->prefix('$')
            ->readonly()
            ->numeric()
            ->placeholder('اختر كاشير لرؤية الرصيد')
            ->extraInputAttributes(['style' => 'font-weight: bold; color: #10b981;'])
            ->dehydrated(false),

          Forms\Components\Textarea::make('note')
            ->label('ملاحظات إضافية')
            ->columnSpanFull(),
        ])->columns(2),
    ]);
  }

  public static function table(Table $table): Table
  {
    return $table->columns([
      Tables\Columns\TextColumn::make('date')
        ->label('التاريخ')
        ->date()
        ->sortable(),

      Tables\Columns\TextColumn::make('salesPoint.name')
        ->label('نقطة البيع')
        ->badge()
        ->color('gray'),

      Tables\Columns\TextColumn::make('name')
        ->label('البيان / المادة')
        ->searchable(),

      Tables\Columns\TextColumn::make('manager.user.name')
        ->label('المدير المسلم')
        ->searchable(),

      Tables\Columns\TextColumn::make('cashier.user.name')
        ->label('المستلم')
        ->searchable(),

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

      Tables\Columns\TextColumn::make('amount')
        ->label('الكمية')
        ->money('USD', locale: 'en_US')
        ->sortable()
        ->summarize([
          Tables\Columns\Summarizers\Sum::make()
            ->label('الإجمالي')
            ->money('USD', locale: 'en_US'),
        ]),

    ])
      ->defaultSort('created_at', 'DESC')
      ->filters([
        Tables\Filters\SelectFilter::make('trans_type')
          ->label('نوع الحركة')
          ->options([
            'deposit' => 'إيداع',
            'withdraw' => 'سحب',
          ]),

        Tables\Filters\SelectFilter::make('sales_point_id')
          ->label('نقطة البيع')
          ->relationship('salesPoint', 'name'),

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
      ])
      ->actions([
        Tables\Actions\EditAction::make(),

        Tables\Actions\DeleteAction::make()
          ->label('حذف')
          ->before(function (Tables\Actions\DeleteAction $action, SalesPointCashierTrans $record) {
            if ($record->salesPoint()->exists()) {
              \Filament\Notifications\Notification::make()
                ->danger()
                ->title('لا يمكن حذف التحويل')
                ->body('هذا التحويل مرتبط بنقطة بيع وسجلات محاسبية.')
                ->persistent()
                ->send();

              $action->halt();
            }
          }),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
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
      'index' => Pages\ListSalesPointCashierTrans::route('/'),
      'create' => Pages\CreateSalesPointCashierTrans::route('/create'),
      'edit' => Pages\EditSalesPointCashierTrans::route('/{record}/edit'),
    ];
  }

  public static function getEloquentQuery(): Builder
  {
    $user = auth()->user();
    $query = parent::getEloquentQuery()->with([
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