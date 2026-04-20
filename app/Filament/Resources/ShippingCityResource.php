<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShippingCityResource\Pages;
use Filament\Resources\Resource;
use App\Models\ShippingCity;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\App;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ShippingCityResource extends Resource
{
  protected static ?string $model = ShippingCity::class;

  protected static ?string $navigationIcon = 'heroicon-o-map-pin';
  protected static ?int $navigationSort = 6;
  protected static ?string $navigationLabel = 'مناطق الشحن';
  protected static ?string $pluralModelLabel = 'مناطق الشحن';
  protected static ?string $modelLabel = 'منطقة شحن';
  protected static ?string $navigationGroup = 'شحن و استيراد';

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make('معلومات المنطقة')
          ->schema([
            Forms\Components\Tabs::make('Languages')
              ->tabs([
                Forms\Components\Tabs\Tab::make('English')
                  ->schema([
                    Forms\Components\TextInput::make('city_name.en')
                      ->label('City Name (EN)')
                      ->required(),
                  ]),
                Forms\Components\Tabs\Tab::make('Arabic')
                  ->schema([
                    Forms\Components\TextInput::make('city_name.ar')
                      ->label('اسم المدينة (AR)')
                      ->required(),
                  ]),
              ])->columnSpanFull(),

            Forms\Components\TextInput::make('estimated_delivery')
              ->label('وقت التوصيل المتوقع')
              ->placeholder('مثال: 24-48 ساعة')
              ->required(),

            Forms\Components\TextInput::make('shipping_fee')
              ->label('رسوم الشحن')
              ->numeric()
              ->prefix('USD')
              ->required(),

            Forms\Components\Toggle::make('is_free_shipping')
              ->label('شحن مجاني')
              ->default(false),

            Forms\Components\Toggle::make('is_active')
              ->label('تفعيل المنطقة')
              ->default(true),
          ])->columns(2),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('city_name')
          ->label('اسم المدينة')
          ->getStateUsing(fn(ShippingCity $record) => $record->city_name[App::getLocale()] ?? $record->city_name['en'] ?? '')
          ->searchable(query: function (Builder $query, string $search): Builder {
            $locale = App::getLocale();
            return $query->where("city_name->$locale", 'like', "%{$search}%")
              ->orWhere("city_name->en", 'like', "%{$search}%");
          }),

        Tables\Columns\TextColumn::make('estimated_delivery')
          ->label('وقت التوصيل')
          ->badge()
          ->color('info')
          ->searchable(),

        Tables\Columns\TextColumn::make('shipping_fee')
          ->label('الرسوم')
          ->money('USD', locale: 'en_US')
          ->color('success')
          ->sortable(),

        Tables\Columns\IconColumn::make('is_free_shipping')
          ->label('شحن مجاني')
          ->boolean(),

        Tables\Columns\ToggleColumn::make('is_active')
          ->label('نشط')
          ->toggleable(isToggledHiddenByDefault: true),


        Tables\Columns\TextColumn::make('created_at')
          ->label('تاريخ الإضافة')
          ->date()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->defaultSort('created_at', 'DESC')
      ->filters([
        Tables\Filters\TernaryFilter::make('is_free_shipping')
          ->label('شحن مجاني فقط'),
        Tables\Filters\TernaryFilter::make('is_active')
          ->label('المناطق النشطة'),
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make()
          ->before(function (Tables\Actions\DeleteAction $action, ShippingCity $record) {
            if ($record->checkouts()->exists()) {
              \Filament\Notifications\Notification::make()
                ->danger()
                ->title('خطأ في الحذف')
                ->body('لا يمكن حذف هذه المنطقة لارتباطها بعمليات دفع (Checkouts) مسجلة.')
                ->send();
              $action->halt();
            }
          }),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ]);
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListShippingCities::route('/'),
      'create' => Pages\CreateShippingCity::route('/create'),
      'edit' => Pages\EditShippingCity::route('/{record}/edit'),
    ];
  }
}
