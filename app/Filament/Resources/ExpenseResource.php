<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Models\Expense;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;

class ExpenseResource extends Resource
{
  protected static ?string $model = Expense::class;
  protected static ?string $navigationIcon = 'heroicon-o-receipt-refund';
  protected static ?string $navigationGroup = 'الإدارة المالية';
  protected static ?string $navigationLabel = 'المصروفات العامة';
  protected static ?string $pluralModelLabel = 'المصروفات العامة';
  protected static ?string $modelLabel = 'مصروف';

  protected static ?int $navigationSort = 3;

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Section::make('بيانات المصروف')
          ->schema([
            Select::make('user_id')
              ->label('المسؤول عن الصرف')
              ->relationship('user', 'name')
              ->default(auth()->id())
              ->required()
              ->searchable(),

            TextInput::make('amount')
              ->label('المبلغ المسحوب')
              ->numeric()
              ->prefix('$')
              ->required(),

            DateTimePicker::make('expense_date')
              ->label('تاريخ العملية')
              ->default(now())
              ->required(),

            TextInput::make('reason')
              ->label('البيان (السبب)')
              ->required()
              ->columnSpanFull(),
          ])
          ->columns(3),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('expense_date')
          ->label('التاريخ')
          ->dateTime()
          ->searchable()
          ->sortable(),

        TextColumn::make('user.name')
          ->label('الموظف')
          ->searchable()
          ->sortable(),

        TextColumn::make('reason')
          ->label('البيان')
          ->searchable()
          ->sortable(),


        TextColumn::make('amount')
          ->label('المبلغ')
          ->money('USD', locale: 'en_US')
          ->searchable()
          ->sortable()
          ->color('warning'),
      ])
      ->filters([
        SelectFilter::make('user_id')
          ->label('تصفية حسب الموظف')
          ->relationship('user', 'name')
          ->searchable()
          ->preload(),

        Filter::make('expense_date')
          ->form([
            DatePicker::make('from')->label('من تاريخ'),
            DatePicker::make('until')->label('إلى تاريخ'),
          ])
          ->query(function (Builder $query, array $data): Builder {
            return $query
              ->when(
                $data['from'],
                fn(Builder $query, $date): Builder => $query->whereDate('expense_date', '>=', $date),
              )
              ->when(
                $data['until'],
                fn(Builder $query, $date): Builder => $query->whereDate('expense_date', '<=', $date),
              );
          })
          ->indicateUsing(function (array $data): array {
            $indicators = [];
            if ($data['from'] ?? null) {
              $indicators['from'] = 'تبدأ من: ' . \Carbon\Carbon::parse($data['from'])->toFormattedDateString();
            }
            if ($data['until'] ?? null) {
              $indicators['until'] = 'حتى تاريخ: ' . \Carbon\Carbon::parse($data['until'])->toFormattedDateString();
            }
            return $indicators;
          }),

        TrashedFilter::make()
          ->label('حالة السجلات')
          ->native(false),
      ])
      ->defaultSort('created_at', 'DESC')
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
          DeleteBulkAction::make()->label('أرشفة المحدد'),
          ForceDeleteBulkAction::make()->label('حذف نهائي'),
          RestoreBulkAction::make()->label('استعادة المحدد'),
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
      'index' => Pages\ListExpenses::route('/'),
      'create' => Pages\CreateExpense::route('/create'),
      'edit' => Pages\EditExpense::route('/{record}/edit'),
    ];
  }
}