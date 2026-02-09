<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductImportResource\Pages;
use App\Models\ProductImport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\ProductImportResource\RelationManagers\ProductVariantsRelationManager;

class ProductImportResource extends Resource
{
  protected static ?string $model = ProductImport::class;

  protected static ?string $navigationIcon = 'heroicon-o-truck';
  protected static ?string $navigationLabel = ' الموردين';
  protected static ?string $pluralModelLabel = ' الموردين';
  protected static ?string $modelLabel = ' مورد';
  protected static ?string $navigationGroup = 'شحن و استيراد';

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make('معلومات المورد والشحنة')
          ->description('إدخال تفاصيل الجهة المصدرة وبيانات الوصول')
          ->schema([
            Forms\Components\TextInput::make('supplier_name')
              ->label('اسم المورد / الشركة')
              ->required()
              ->maxLength(255),

            Forms\Components\TextInput::make('address')
              ->label('عنوان المورد / بلد المنشأ')
              ->required()
              ->placeholder('مثال: الصين - كوانزو')
              ->maxLength(255),

            Forms\Components\Textarea::make('notes')
              ->label('ملاحظات إضافية')
              ->columnSpanFull(),
          ])->columns(3),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('supplier_name')
          ->label('المورد')
          ->searchable()
          ->sortable()
          ->weight('bold'),

        Tables\Columns\TextColumn::make('address')
          ->label('العنوان')
          ->icon('heroicon-m-map-pin')
          ->color('gray'),

        Tables\Columns\TextColumn::make('import_date')
          ->label('تاريخ الاستيراد')
          ->date('Y-m-d')
          ->sortable(),

        Tables\Columns\TextColumn::make('product_variants_count')
          ->label('عدد الأصناف')
          ->counts('productVariants')
          ->badge()
          ->color('info'),

        Tables\Columns\TextColumn::make('created_at')
          ->label('تاريخ التسجيل')
          ->dateTime('Y-m-d H:i')
          ->toggleable(isToggledHiddenByDefault: true),

        Tables\Columns\TextColumn::make('notes')
          ->label('ملاحظة')
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        Tables\Filters\Filter::make('import_date')
          ->form([
            Forms\Components\DatePicker::make('from')->label('من تاريخ'),
            Forms\Components\DatePicker::make('until')->label('إلى تاريخ'),
          ])
          ->query(
            fn($query, array $data) => $query
              ->when($data['from'], fn($q) => $q->whereDate('import_date', '>=', $data['from']))
              ->when($data['until'], fn($q) => $q->whereDate('import_date', '<=', $data['until']))
          )
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\ViewAction::make(),
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
      ProductVariantsRelationManager::class,
    ];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListProductImports::route('/'),
      'create' => Pages\CreateProductImport::route('/create'),
      'edit' => Pages\EditProductImport::route('/{record}/edit'),
    ];
  }
}
