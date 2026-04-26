<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductImportResource\Pages;
use App\Models\ProductImport;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use App\Filament\Resources\ProductImportResource\RelationManagers\ProductVariantsRelationManager;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\DeleteAction;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;

class ProductImportResource extends Resource
{
  protected static ?string $model = ProductImport::class;

  protected static ?string $navigationIcon = 'heroicon-o-users';
  protected static ?int $navigationSort = 1;
  protected static ?string $navigationLabel = 'الموردين';
  protected static ?string $pluralModelLabel = ' الموردين';
  protected static ?string $modelLabel = ' مورد';
  protected static ?string $navigationGroup = 'شحن و استيراد';

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Section::make('معلومات المورد والشحنة')
          ->description('إدخال تفاصيل الجهة المصدرة وبيانات الوصول')
          ->schema([
            TextInput::make('supplier_name')
              ->label('اسم المورد / الشركة')
              ->required()
              ->maxLength(255),

            TextInput::make('address')
              ->label('عنوان المورد / بلد المنشأ')
              ->required()
              ->maxLength(255),

            TextInput::make('supplier_phone')
              ->label('رقم الهاتف')
              ->tel(),

            Textarea::make('notes')
              ->label('ملاحظات إضافية')
              ->columnSpanFull(),
          ])->columns(3),
      ]);
  }
  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('supplier_name')
          ->label('المورد')
          ->searchable()
          ->sortable()
          ->weight('bold'),

        TextColumn::make('address')
          ->label('العنوان')
          ->icon('heroicon-m-map-pin')
          ->searchable()
          ->color('gray'),

        TextColumn::make('supplier_phone')
          ->label('الهاتف')
          ->searchable(),

        TextColumn::make('product_variants_count')
          ->label('عدد الأصناف')
          ->counts('productVariants')
          ->badge()
          ->counts('productVariants')
          ->color('info'),

        TextColumn::make('created_at')
          ->label('تاريخ التسجيل')
          ->dateTime('Y-m-d H:i')
          ->timezone('Asia/Riyadh')
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),

        TextColumn::make('notes')
          ->label('ملاحظة')
          ->toggleable(isToggledHiddenByDefault: true),


        TextColumn::make('total_import_cost')
          ->label('إجمالي المشتريات')
          ->getStateUsing(fn($record) => $record->productVariants->sum(function ($variant) {
            return ($variant->pivot->price * $variant->pivot->quantity) + $variant->pivot->shipping_price;
          }))
          ->money('USD', locale: 'en')->sortable(),


      ])
      ->defaultSort('created_at', 'DESC')
      ->filters([
        Filter::make('created_at')
          ->form([
            DatePicker::make('from')
              ->label('من تاريخ التسجيل'),
            DatePicker::make('until')
              ->label('إلى تاريخ التسجيل'),
          ])
          ->query(
            fn($query, array $data) => $query
              ->when($data['from'], fn($q) => $q->whereDate('created_at', '>=', $data['from']))
              ->when($data['until'], fn($q) => $q->whereDate('created_at', '<=', $data['until']))
          )
          ->indicateUsing(function (array $data): array {
            $indicators = [];
            if ($data['from'] ?? null)
              $indicators[] = 'من: ' . $data['from'];
            if ($data['until'] ?? null)
              $indicators[] = 'إلى: ' . $data['until'];
            return $indicators;
          }),
      ])
      ->actions([
        ViewAction::make()->label('عرض'),
        EditAction::make()->label('تعديل'),
        DeleteAction::make()
          ->label('حذف')
          ->before(function (DeleteAction $action, ProductImport $record) {
            if ($record->productVariants()->exists()) {
              Notification::make()
                ->danger()
                ->title('لا يمكن حذف المورد')
                ->body('هذا المورد مرتبط بعمليات استيراد وأصناف موجودة في النظام. يجب حذف التبعيات أولاً.')
                ->persistent()
                ->send();

              $action->halt();
            }
          }),
      ])
      ->bulkActions([
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