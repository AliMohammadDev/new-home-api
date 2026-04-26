<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaterialResource\Pages;
use App\Models\Material;
use Filament\Forms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;

class MaterialResource extends Resource
{
  protected static ?string $model = Material::class;
  protected static ?int $navigationSort = 4;
  protected static ?string $navigationIcon = 'heroicon-o-cube';
  protected static ?string $navigationLabel = 'المواد';
  protected static ?string $pluralModelLabel = 'المواد';
  protected static ?string $modelLabel = 'مادة';
  protected static ?string $navigationGroup = 'إدارة المنتجات';

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Tabs::make('Languages')
          ->tabs([
            Forms\Components\Tabs\Tab::make('English')
              ->schema([
                Forms\Components\TextInput::make('material.en')
                  ->label('اسم المادة (EN)')
                  ->required(),
              ]),
            Forms\Components\Tabs\Tab::make('Arabic')
              ->schema([
                TextInput::make('material.ar')
                  ->label('اسم المادة (AR)')
                  ->required(),
              ]),
          ]),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('material')
          ->label('الاسم')
          ->getStateUsing(fn(Material $record) => $record->material[App::getLocale()] ?? $record->material['en'] ?? '')
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
        Filter::make('material')
          ->label('بحث بالمادة')
          ->form([
            TextInput::make('material')
              ->label('المادة'),
          ])
          ->query(function (Builder $query, array $data) {
            return $query->when($data['material'] ?? null, fn($q, $color) => $q->where('material', 'like', "%$color%"));
          }),
      ])
      ->actions([
        EditAction::make(),
        ViewAction::make()->label('عرض'),
        DeleteAction::make()
          ->label('حذف')
          ->before(function (DeleteAction $action, Material $record) {
            if ($record->productVariants()->exists()) {
              Notification::make()
                ->danger()
                ->title('خطأ في الحذف')
                ->body('لا يمكن حذف هذا المادة لارتباطه بمنتجات.')
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
      'index' => Pages\ListMaterials::route('/'),
      'create' => Pages\CreateMaterial::route('/create'),
      'edit' => Pages\EditMaterial::route('/{record}/edit'),
      'view' => Pages\ViewMaterial::route('/{record}'),

    ];
  }
}
