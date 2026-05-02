<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonalWithdrawalResource\Pages;
use App\Models\PersonalWithdrawal;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
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
use Filament\Tables\Filters\TrashedFilter;

class PersonalWithdrawalResource extends Resource
{
  protected static ?string $model = PersonalWithdrawal::class;

  protected static ?string $navigationIcon = 'heroicon-o-user-minus';
  protected static ?string $navigationGroup = 'الإدارة المالية';
  protected static ?string $navigationLabel = 'المسحوبات الشخصية';
  protected static ?string $pluralModelLabel = 'المسحوبات العامة';

  protected static ?string $modelLabel = 'سحب جديد';

  protected static ?int $navigationSort = 4;

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Section::make('تسجيل مسحوبات جديدة')
          ->schema([
            TextInput::make('user_name')
              ->label('اسم الشخص')
              ->placeholder('اكتب اسم الشخص هنا')
              ->required(),

            TextInput::make('amount')
              ->label('المبلغ المسحوب')
              ->numeric()
              ->prefix('$')
              ->required(),

            DateTimePicker::make('expense_date')
              ->label('تاريخ السحب')
              ->default(now())
              ->required(),

            TextInput::make('reason')
              ->label('السبب / البيان')
              ->required()
              ->columnSpanFull(),
          ])
          ->columns(1),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('expense_date')
          ->label('التاريخ')
          ->date()
          ->searchable()
          ->sortable(),

        TextColumn::make('user_name')
          ->label('اسم الشخص')
          ->sortable()
          ->searchable(),

        TextColumn::make('amount')
          ->label('المبلغ')
          ->money('USD', locale: 'en_US')
          ->searchable()
          ->sortable()
          ->color('warning'),

        TextColumn::make('reason')
          ->label('البيان'),
      ])
      ->filters([
        Tables\Filters\Filter::make('user_name')
          ->form([
            TextInput::make('user_name')->label('بحث باسم الشخص'),
          ])
          ->query(function (Builder $query, array $data): Builder {
            return $query->when(
              $data['user_name'],
              fn(Builder $query, $name): Builder => $query->where('user_name', 'like', "%{$name}%"),
            );
          }),

        Tables\Filters\Filter::make('expense_date')
          ->form([
            DatePicker::make('from')->label('من تاريخ'),
            DatePicker::make('until')->label('إلى تاريخ'),
          ])
          ->query(function (Builder $query, array $data): Builder {
            return $query
              ->when($data['from'], fn($q, $date) => $q->whereDate('expense_date', '>=', $date))
              ->when($data['until'], fn($q, $date) => $q->whereDate('expense_date', '<=', $date));
          }),

        TrashedFilter::make()->label('المؤرشفة'),
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

  public static function getEloquentQuery(): Builder
  {
    return parent::getEloquentQuery()
      ->forActiveYear()
      ->withTrashed();
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListPersonalWithdrawals::route('/'),
      'create' => Pages\CreatePersonalWithdrawal::route('/create'),
      'edit' => Pages\EditPersonalWithdrawal::route('/{record}/edit'),
    ];
  }
}
