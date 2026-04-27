<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
  protected static ?string $model = User::class;
  protected static ?string $navigationIcon = 'heroicon-o-users';
  protected static ?string $navigationLabel = 'المستخدمون';
  protected static ?string $pluralModelLabel = 'المستخدمون';
  protected static ?string $modelLabel = 'مستخدم';
  protected static ?string $navigationGroup = 'إدارة المستخدمين';

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        TextInput::make('name')
          ->label('الاسم')
          ->required()
          ->maxLength(255),

        TextInput::make('email')
          ->label('البريد الإلكتروني')
          ->email()
          ->required()
          ->unique(ignoreRecord: true),



        Select::make('roles')
          ->label('الأدوار')
          ->multiple()
          ->relationship('roles', 'name')
          ->getOptionLabelFromRecordUsing(fn($record) => $record->display_name[app()->getLocale()] ?? $record->display_name['ar'] ?? $record->name)
          ->preload(),

        Toggle::make('is_active')
          ->label('حساب نشط')
          ->default(true)
          ->onColor('success')
          ->offColor('danger'),

        TextInput::make('password')
          ->label('كلمة المرور')
          ->password()
          ->required(fn($record) => $record === null)
          // ->disabled(fn($record) => $record !== null)
          ->dehydrateStateUsing(fn($state) => filled($state) ? bcrypt($state) : null)
          ->dehydrated(fn($state) => filled($state)),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('id')
          ->label('ID')
          ->sortable()
          ->searchable(),

        TextColumn::make('name')
          ->label('الاسم')
          ->sortable()
          ->searchable(),

        TextColumn::make('email')
          ->label('البريد الإلكتروني')
          ->sortable()
          ->searchable(),

        ToggleColumn::make('is_active')
          ->label('الحالة')
          ->onIcon('heroicon-m-check-circle')
          ->offIcon('heroicon-m-x-circle')
          ->onColor('success')
          ->offColor('danger'),

        // TextColumn::make('roles.name')
        //   ->label('الأدوار')
        //   ->badge()
        //   ->color(fn(string $state): string => match ($state) {
        //     'super_admin' => 'danger',
        //     'admin' => 'warning',
        //     'customer' => 'success',
        //     default => 'gray',
        //   }),

        TextColumn::make('roles.display_name')
          ->label('الأدوار')
          ->badge()
          ->formatStateUsing(function ($state, $record) {
            $locale = app()->getLocale();
            return $state[$locale] ?? $state['ar'] ?? $record->name;
          })
          ->color(fn($record): string => match ($record->name) {
            'super_admin' => 'danger',
            'admin' => 'warning',
            'customer' => 'success',
            default => 'gray',
          }),

        TextColumn::make('created_at')
          ->label('تاريخ الإنشاء')
          ->dateTime('Y-m-d')
          ->sortable()
          ->searchable(),
      ])
      ->defaultSort('created_at', 'desc')
      ->filters([
        Tables\Filters\TernaryFilter::make('is_active')
          ->label('حالة الحساب')
          ->placeholder('الكل')
          ->trueLabel('المستخدمون النشطون')
          ->falseLabel('المستخدمون المعطلون'),

        Tables\Filters\SelectFilter::make('roles')
          ->label('الدور')
          ->relationship('roles', 'name')
          ->getOptionLabelFromRecordUsing(fn($record) => $record->display_name[app()->getLocale()] ?? $record->display_name['ar'] ?? $record->name)
          ->preload(),
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\ViewAction::make()->label('عرض'),
        Tables\Actions\DeleteAction::make()
          ->label('حذف')
          ->before(function (Tables\Actions\DeleteAction $action, User $record) {
            if (
              $record->reviews()->exists() ||
              $record->wishlist()->exists() ||
              $record->carts()->exists() ||
              $record->checkouts()->exists() ||
              $record->reviews()->exists() ||
              $record->orders()->exists() ||
              $record->salesPoints()->exists()
            ) {
              Notification::make()
                ->danger()
                ->title('لا يمكن حذف المستخدم')
                ->body('هذا المستخدم مرتبط بسجلات أخرى (طلبات، سلة، أو نقاط بيع). يجب حذف التبعيات أولاً.')
                ->persistent()
                ->send();

              $action->halt();
            }
          }),

      ])
      ->bulkActions([
        // Tables\Actions\BulkActionGroup::make([
        //   Tables\Actions\DeleteBulkAction::make()
        //     ->label('حذف المحدد')
        //     ->before(function (Tables\Actions\DeleteBulkAction $action, \Illuminate\Support\Collection $records) {
        //       foreach ($records as $record) {
        //         if (
        //           $record->orders()->exists() ||
        //           $record->checkouts()->exists() ||
        //           $record->carts()->exists()
        //         ) {
        //           Notification::make()
        //             ->danger()
        //             ->title('عملية غير مسموحة')
        //             ->body("المستخدم {$record->name} مرتبط ببيانات نشطة، لا يمكن حذف المجموعة.")
        //             ->send();

        //           $action->halt();
        //         }
        //       }
        //     })
        //     ->requiresConfirmation()
        //     ->modalHeading('تأكيد الحذف')
        //     ->modalDescription('هل أنت متأكد من حذف المستخدمين المحددين؟')
        //     ->modalSubmitActionLabel('نعم، احذف'),
        // ]),
      ]);
  }

  public static function getEloquentQuery(): Builder
  {
    return parent::getEloquentQuery()
      ->whereHas('roles', function ($query) {
        $query->where('name', '!=', 'customer');
      });
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
      'index' => Pages\ListUsers::route('/'),
      'create' => Pages\CreateUser::route('/create'),
      'edit' => Pages\EditUser::route('/{record}/edit'),
      'view' => Pages\ViewUser::route('/{record}'),
    ];
  }

}