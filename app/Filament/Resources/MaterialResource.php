<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaterialResource\Pages;
use App\Models\Material;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;

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
        Forms\Components\Tabs::make('Languages')
          ->tabs([
            Forms\Components\Tabs\Tab::make('English')
              ->schema([
                Forms\Components\TextInput::make('material.en')
                  ->label('اسم المادة (EN)')
                  ->required(),
              ]),
            Forms\Components\Tabs\Tab::make('Arabic')
              ->schema([
                Forms\Components\TextInput::make('material;.ar')
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
        Tables\Columns\TextColumn::make('material')
          ->label('الاسم')
          ->getStateUsing(fn(Material $record) => $record->material[App::getLocale()] ?? $record->material['en'] ?? '')
          ->sortable()
          ->searchable(),
        Tables\Columns\TextColumn::make('created_at')
          ->label('تاريخ الإنشاء')
          ->sortable()
          ->searchable()
          ->date(),
      ])
      ->defaultSort('created_at', 'desc')
      ->filters([
        Tables\Filters\Filter::make('color')
          ->label('بحث باللون')
          ->form([
            Forms\Components\TextInput::make('color')->label('اللون'),
          ])
          ->query(function (Builder $query, array $data) {
            return $query->when($data['color'] ?? null, fn($q, $color) => $q->where('color', 'like', "%$color%"));
          }),
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\ViewAction::make()->label('عرض'),
        Tables\Actions\DeleteAction::make()
          ->label('حذف')
          ->before(function (Tables\Actions\DeleteAction $action, Material $record) {
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
