<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeliveryCompanyResource\Pages;
use App\Models\DeliveryCompany;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Notifications\Notification;

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
        Section::make('معلومات شركة التوصيل')
          ->schema([
            Select::make('user_id')
              ->label('المستخدم المسؤول')
              ->relationship('user', 'name')
              ->searchable()
              ->preload()
              ->required(),

            TextInput::make('name')
              ->label('اسم الشركة')
              ->required()
              ->maxLength(255),

            TextInput::make('phone')
              ->label('رقم التواصل')
              ->tel(),

            Textarea::make('address')
              ->label('العنوان')
              ->columnSpanFull(),

            Toggle::make('is_active')
              ->label('حالة الشركة')
              ->default(true),
          ])->columns(2),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('name')
          ->label('اسم الشركة')
          ->searchable()
          ->sortable(),

        TextColumn::make('user.name')
          ->label('المسؤول')
          ->sortable(),

        TextColumn::make('phone')
          ->label('الهاتف')
          ->copyable()
          ->icon('heroicon-m-phone'),

        IconColumn::make('is_active')
          ->label('نشط')
          ->toggleable(isToggledHiddenByDefault: true)
          ->boolean(),

        TextColumn::make('created_at')
          ->label('تاريخ الإضافة')
          ->dateTime()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        TernaryFilter::make('is_active')
          ->label('الشركات النشطة فقط'),
      ])
      ->actions([
        EditAction::make(),
        DeleteAction::make()
          ->before(function (DeleteAction $action, DeliveryCompany $record) {
            if ($record->orders()->exists()) {
              Notification::make()
                ->danger()
                ->title('لا يمكن حذف الشركة')
                ->body('هذه الشركة مرتبطة بطلبات مسجلة في النظام، يرجى حذف الطلبات أولاً أو تعطيل حساب الشركة بدلاً من حذفه.')
                ->persistent()
                ->send();

              $action->cancel();
            }
          }),
      ])
      ->bulkActions([
        BulkActionGroup::make([
          DeleteBulkAction::make(),
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