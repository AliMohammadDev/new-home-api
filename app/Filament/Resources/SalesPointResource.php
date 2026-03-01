<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesPointResource\Pages;
use App\Filament\Resources\SalesPointResource\RelationManagers;
use App\Models\SalesPoint;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;

class SalesPointResource extends Resource
{
  protected static ?string $model = SalesPoint::class;

  protected static ?string $navigationIcon = 'heroicon-o-map-pin';

  protected static ?string $navigationGroup = 'إدارة المبيعات';

  protected static ?int $navigationSort = 1;

  protected static ?string $navigationLabel = 'نقاط البيع';

  protected static ?string $pluralModelLabel = 'نقطة بيع';
  protected static ?string $modelLabel = 'نقطة بيع';

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Section::make('معلومات نقطة البيع')
          ->schema([
            TextInput::make('name')
              ->label('اسم نقطة البيع')
              ->required()
              ->maxLength(255),

            TextInput::make('location')
              ->label('الموقع')
              ->placeholder('مثلاً: دمشق، المزة'),

            TextInput::make('phone')
              ->label('رقم الهاتف')
              ->tel(),

            Toggle::make('is_active')
              ->label('حالة نقطة البيع')
              ->onIcon('heroicon-m-check-circle')
              ->offIcon('heroicon-m-x-circle')
              ->onColor('success')
              ->offColor('gray')
              ->inline(false)
          ])->columns(2),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('name')
          ->label('الاسم')
          ->searchable()
          ->sortable(),

        TextColumn::make('location')
          ->label('الموقع')
          ->limit(30),

        Tables\Columns\TextColumn::make('phone')
          ->label('الهاتف')
          ->formatStateUsing(fn(string $state): string => "📞 " . $state)
          ->extraAttributes(['class' => 'font-mono']),

        IconColumn::make('is_active')
          ->label('نشط')
          ->boolean()
          ->sortable(),

        TextColumn::make('created_at')
          ->label('تاريخ الإنشاء')
          ->dateTime('d/m/Y')
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        Tables\Filters\TernaryFilter::make('is_active')
          ->label('تصفية حسب الحالة'),
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
      'index' => Pages\ListSalesPoints::route('/'),
      'create' => Pages\CreateSalesPoint::route('/create'),
      'edit' => Pages\EditSalesPoint::route('/{record}/edit'),
    ];
  }
}
