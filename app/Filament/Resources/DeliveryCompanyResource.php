<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeliveryCompanyResource\Pages;
use App\Models\DeliveryCompany;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DeliveryCompanyResource extends Resource
{
  protected static ?string $model = DeliveryCompany::class;
  protected static ?string $navigationIcon = 'heroicon-o-truck';
  protected static ?string $navigationGroup = 'إدارة التوصيل';
  protected static ?string $navigationLabel = 'شركات التوصيل';
  protected static ?string $pluralModelLabel = 'شركات التوصيل';
  protected static ?string $modelLabel = 'شركة توصيل';


  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make('معلومات شركة التوصيل')
          ->schema([
            Forms\Components\Select::make('user_id')
              ->label('المستخدم المسؤول')
              ->relationship('user', 'name')
              ->searchable()
              ->preload()
              ->required(),

            Forms\Components\TextInput::make('name')
              ->label('اسم الشركة')
              ->required()
              ->maxLength(255),

            Forms\Components\TextInput::make('phone')
              ->label('رقم التواصل')
              ->tel(),



            Forms\Components\Textarea::make('address')
              ->label('العنوان')
              ->columnSpanFull(),

            Forms\Components\Toggle::make('is_active')
              ->label('حالة الشركة')
              ->default(true),
          ])->columns(2),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('name')
          ->label('اسم الشركة')
          ->searchable()
          ->sortable(),

        Tables\Columns\TextColumn::make('user.name')
          ->label('المسؤول')
          ->sortable(),

        Tables\Columns\TextColumn::make('phone')
          ->label('الهاتف')
          ->copyable()
          ->icon('heroicon-m-phone'),

        Tables\Columns\IconColumn::make('is_active')
          ->label('نشط')
          ->toggleable(isToggledHiddenByDefault: true)
          ->boolean(),

        Tables\Columns\TextColumn::make('created_at')
          ->label('تاريخ الإضافة')
          ->dateTime()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        Tables\Filters\TernaryFilter::make('is_active')
          ->label('الشركات النشطة فقط'),
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
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
    return parent::getEloquentQuery()->with('user');
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListDeliveryCompanies::route('/'),
      'create' => Pages\CreateDeliveryCompany::route('/create'),
      'edit' => Pages\EditDeliveryCompany::route('/{record}/edit'),
    ];
  }
}