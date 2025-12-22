<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\Widgets\UsersCountWidget;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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

        Select::make('role')
          ->label('الدور')
          ->required()
          ->options([
            'admin' => 'مدير',
            'user' => 'مستخدم',
          ]),

        TextInput::make('password')
          ->label('كلمة المرور')
          ->password()
          ->required(fn($record) => $record === null)
          ->disabled(fn($record) => $record !== null)
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
          ->searchable()
          ->sortable()
          ->searchable(),

        TextColumn::make('email')
          ->label('البريد الإلكتروني')
          ->sortable()
          ->searchable(),

        TextColumn::make('role')
          ->label('الدور')
          ->badge()
          ->color(fn(string $state) => match ($state) {
            'admin' => 'danger',
            'user' => 'success',
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
        SelectFilter::make('role')
          ->label('الدور')
          ->options([
            'admin' => 'مدير',
            'customer' => 'مستخدم',
          ]),
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
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
      'index' => Pages\ListUsers::route('/'),
      'create' => Pages\CreateUser::route('/create'),
      'edit' => Pages\EditUser::route('/{record}/edit'),
    ];
  }

}