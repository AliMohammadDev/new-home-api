<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ColorResource\Pages;
use App\Models\Color;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;

class ColorResource extends Resource
{
  protected static ?string $model = Color::class;
  protected static ?int $navigationSort = 2;
  protected static ?string $navigationIcon = 'heroicon-o-swatch';
  protected static ?string $navigationLabel = 'الألوان';
  protected static ?string $pluralModelLabel = 'الألوان';
  protected static ?string $modelLabel = 'لون';
  protected static ?string $navigationGroup = 'إدارة المنتجات';

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Tabs::make('Languages')
          ->tabs([
            Forms\Components\Tabs\Tab::make('English')
              ->schema([
                Forms\Components\TextInput::make('color.en')
                  ->label('اسم اللون (EN)')
                  ->required(),
              ]),
            Forms\Components\Tabs\Tab::make('Arabic')
              ->schema([
                TextInput::make('color.ar')
                  ->label('اسم اللون (AR)')
                  ->required(),
              ]),
          ]),
        ColorPicker::make('hex_code')
          ->label('كود اللون')
          ->required()
          ->format('hex'),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        ColorColumn::make('hex_code')
          ->label('اللون')
          ->copyable(),

        TextColumn::make('color')
          ->label('الاسم')
          ->getStateUsing(fn(Color $record) => $record->color[App::getLocale()] ?? $record->color['en'] ?? '')
          ->sortable()
          ->searchable(),

        TextColumn::make('created_at')
          ->label('تاريخ الإنشاء')
          ->sortable()
          ->searchable()
          ->date(),
      ])
      ->defaultSort('created_at', 'desc')
      ->filters([
        Filter::make('color')
          ->label('بحث باللون')
          ->form([
            TextInput::make('color')->label('اللون'),
          ])
          ->query(function (Builder $query, array $data) {
            return $query->when($data['color'] ?? null, fn($q, $color) => $q->where('color', 'like', "%$color%"));
          }),
      ])
      ->actions([
        EditAction::make(),
        ViewAction::make()->label('عرض'),
        DeleteAction::make()
          ->label('حذف')
          ->before(function (DeleteAction $action, Color $record) {
            if ($record->productVariants()->exists()) {
              Notification::make()
                ->danger()
                ->title('خطأ في الحذف')
                ->body('لا يمكن حذف هذا اللون لارتباطه بمنتجات.')
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

  public static function getRelations(): array
  {
    return [
      //
    ];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListColors::route('/'),
      'create' => Pages\CreateColor::route('/create'),
      'edit' => Pages\EditColor::route('/{record}/edit'),
      'view' => Pages\ViewColor::route('/{record}'),
    ];
  }
}