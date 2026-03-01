<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesPointManagerResource\Pages;
use App\Filament\Resources\SalesPointManagerResource\RelationManagers;
use App\Models\SalesPointManager;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SalesPointManagerResource extends Resource
{
  protected static ?string $model = SalesPointManager::class;

  protected static ?string $navigationIcon = 'heroicon-o-user-group';
  protected static ?string $navigationGroup = 'إدارة المبيعات';
  protected static ?string $navigationLabel = 'مدراء النقاط';
  protected static ?string $pluralModelLabel = 'مدراء نقاط البيع';
  protected static ?string $modelLabel = 'تعيين مدير';

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make('ربط مدير بنقطة بيع')
          ->schema([
            Forms\Components\Select::make('user_id')
              ->label('المستخدم (مدير نقطة البيع)')
              ->relationship('user', 'name')
              ->searchable()
              ->preload()
              ->required(),

            Forms\Components\Select::make('sales_point_id')
              ->label('نقطة البيع')
              ->relationship('salesPoint', 'name')
              ->searchable()
              ->preload()
              ->required(),

            TextInput::make('phone')
              ->label('رقم الهاتف')
              ->tel(),

          ])->columns(2),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('salesPoint.name')
          ->label('نقطة البيع')
          ->sortable()
          ->searchable()
          ->badge()
          ->color('info'),

        Tables\Columns\TextColumn::make('phone')
          ->label('الهاتف')
          ->formatStateUsing(fn(string $state): string => "📞 " . $state)
          ->extraAttributes(['class' => 'font-mono']),


        Tables\Columns\TextColumn::make('user.name')
          ->label('اسم المدير')
          ->sortable()
          ->searchable()
          ->icon('heroicon-m-user'),

        Tables\Columns\TextColumn::make('user.email')
          ->label('البريد الإلكتروني')
          ->color('gray'),



        Tables\Columns\TextColumn::make('created_at')
          ->label('تاريخ التعيين')
          ->dateTime('d/m/Y')
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        Tables\Filters\SelectFilter::make('sales_point_id')
          ->label('تصفية حسب النقطة')
          ->relationship('salesPoint', 'name'),
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\DeleteBulkAction::make(),
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
      'index' => Pages\ListSalesPointManagers::route('/'),
      'create' => Pages\CreateSalesPointManager::route('/create'),
      'edit' => Pages\EditSalesPointManager::route('/{record}/edit'),
    ];
  }
}
