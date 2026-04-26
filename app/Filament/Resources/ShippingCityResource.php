<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShippingCityResource\Pages;
use Filament\Resources\Resource;
use App\Models\ShippingCity;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;

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
        Section::make('معلومات المنطقة')
          ->schema([
            Tabs::make('Languages')
              ->tabs([
                Forms\Components\Tabs\Tab::make('English')
                  ->schema([
                    TextInput::make('city_name.en')
                      ->label('City Name (EN)')
                      ->required(),
                  ]),
                Forms\Components\Tabs\Tab::make('Arabic')
                  ->schema([
                    TextInput::make('city_name.ar')
                      ->label('اسم المدينة (AR)')
                      ->required(),
                  ]),
              ])->columnSpanFull(),

            TextInput::make('estimated_delivery')
              ->label('وقت التوصيل المتوقع')
              ->placeholder('مثال: 24-48 ساعة')
              ->required(),

            TextInput::make('shipping_fee')
              ->label('رسوم الشحن')
              ->numeric()
              ->prefix('USD')
              ->required(),

            Toggle::make('is_free_shipping')
              ->label('شحن مجاني')
              ->default(false),

            Toggle::make('is_active')
              ->label('تفعيل المنطقة')
              ->default(true),
          ])->columns(2),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('city_name')
          ->label('اسم المدينة')
          ->getStateUsing(fn(ShippingCity $record) => $record->city_name[App::getLocale()] ?? $record->city_name['en'] ?? '')
          ->searchable(query: function (Builder $query, string $search): Builder {
            $locale = App::getLocale();
            return $query->where("city_name->$locale", 'like', "%{$search}%")
              ->orWhere("city_name->en", 'like', "%{$search}%");
          }),

        TextColumn::make('estimated_delivery')
          ->label('وقت التوصيل')
          ->badge()
          ->color('info')
          ->searchable(),

        TextColumn::make('shipping_fee')
          ->label('الرسوم')
          ->money('USD', locale: 'en_US')
          ->color('success')
          ->sortable(),

        IconColumn::make('is_free_shipping')
          ->label('شحن مجاني')
          ->boolean(),

        ToggleColumn::make('is_active')
          ->label('نشط')
          ->toggleable(isToggledHiddenByDefault: true),

        TextColumn::make('created_at')
          ->label('تاريخ الإضافة')
          ->date()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->defaultSort('created_at', 'DESC')
      ->filters([
        TernaryFilter::make('is_free_shipping')
          ->label('شحن مجاني فقط'),
        TernaryFilter::make('is_active')
          ->label('المناطق النشطة'),
      ])
      ->actions([
        EditAction::make(),
        DeleteAction::make()
          ->before(function (DeleteAction $action, ShippingCity $record) {
            if ($record->checkouts()->exists()) {
              Notification::make()
                ->danger()
                ->title('خطأ في الحذف')
                ->body('لا يمكن حذف هذه المنطقة لارتباطها بعمليات دفع (Checkouts) مسجلة.')
                ->send();
              $action->halt();
            }
          }),
      ])
      ->bulkActions([
        BulkActionGroup::make([
          DeleteBulkAction::make(),
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