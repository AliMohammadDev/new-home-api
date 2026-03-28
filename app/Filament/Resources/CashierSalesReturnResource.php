<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashierSalesReturnResource\Pages;
use App\Models\CashierSalesReturn;
use App\Models\SalesPointCashier;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;

class CashierSalesReturnResource extends Resource
{
  protected static ?string $model = CashierSalesReturn::class;

  protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

  protected static ?string $navigationGroup = 'إدارة المبيعات';
  protected static ?int $navigationSort = 6;
  protected static ?string $navigationLabel = 'مرتجعات الكاشير (الأصناف) ';
  protected static ?string $pluralModelLabel = 'أصناف مرتجعة';
  protected static ?string $modelLabel = 'مرتجع صنف';

  public static function form(Form $form): Form
  {
    return $form->schema([
      Forms\Components\Section::make('تفاصيل المادة المرتجعة')
        ->schema([
          Forms\Components\Select::make('sales_point_cashier_id')
            ->label('الكاشير')
            ->relationship(
              name: 'cashier',
              titleAttribute: 'id',
              modifyQueryUsing: fn(Builder $query) => $query->whereHas(
                'user',
                fn(Builder $userQuery) => $userQuery->role('sales_point_cashier')
              )
            )
            ->getOptionLabelFromRecordUsing(fn($record) => $record->user?->name ?? 'غير محدد')

            ->default(fn() => SalesPointCashier::where('user_id', auth()->id())->value('id'))

            ->disabled(fn() => auth()->check() && auth()->user()->hasRole('sales_point_cashier'))

            ->dehydrated()

            ->searchable()
            ->preload()
            ->required(),

          Forms\Components\Select::make('product_variant_id')
            ->label('المنتج المرتجع')
            ->relationship('variant', 'sku')
            ->getOptionLabelFromRecordUsing(function ($record) {
              $productName = $record->product->name['ar'] ?? 'منتج غير مسمى';
              $sku = $record->sku ?? 'بدون SKU';
              return "{$productName} - {$sku}";
            })
            ->searchable(['sku'])
            ->preload()
            ->required()
            ->live()
            ->afterStateUpdated(function ($state, Forms\Set $set) {
              if (!$state)
                return;

              $variant = \App\Models\ProductVariant::find($state);
              if ($variant) {
                $set('price', $variant->price);
                $set('full_price', (float) $variant->price);
              }
            }),

          Forms\Components\TextInput::make('quantity')
            ->label('الكمية المرتجعة')
            ->numeric()
            ->default(1)
            ->required()
            ->live()
            ->afterStateUpdated(fn($state, Forms\Get $get, Forms\Set $set) =>
              $set('full_price', (float) $state * (float) ($get('price') ?? 0))),

          Forms\Components\TextInput::make('price')
            ->label('سعر الوحدة')
            ->numeric()
            ->disabled()
            ->dehydrated()
            ->required()
            ->prefix('USD'),

          Forms\Components\TextInput::make('full_price')
            ->label('الإجمالي المرتجع')
            ->numeric()
            ->readonly()
            ->prefix('USD'),
        ])->columns(2),
    ]);
  }

  public static function table(Table $table): Table
  {
    return $table->columns([
      Tables\Columns\TextColumn::make('fatora.id')
        ->label('رقم فاتورة المرتجع')
        ->sortable()
        ->searchable(query: function (Builder $query, string $search): Builder {
          return $query->whereHas('fatora', function (Builder $q) use ($search) {
            $q->where('id', 'like', "%{$search}%");
          });
        }),
      Tables\Columns\TextColumn::make('variant.product.name')
        ->label('المنتج')
        ->getStateUsing(
          fn($record) =>
          $record->variant?->product?->name[App::getLocale()] ??
          $record->variant?->product?->name['en'] ?? ''
        )
        ->searchable(query: function (Builder $query, string $search): Builder {
          return $query->whereHas('variant.product', function (Builder $q) use ($search) {
            $locale = App::getLocale();
            $q->where("name->$locale", 'like', "%{$search}%")
              ->orWhere("name->en", 'like', "%{$search}%");
          });
        }),
      Tables\Columns\TextColumn::make('quantity')->label('الكمية')->color('danger'),
      Tables\Columns\TextColumn::make('full_price')
        ->label('الإجمالي')
        ->money('USD', locale: 'en_US')

        ->summarize(
          Tables\Columns\Summarizers\Sum::make()
            ->label('إجمالي المرتجعات')
            ->money('USD', locale: 'en_US')
        ),


      Tables\Columns\TextColumn::make('cashier.user.name')
        ->label('بواسطة الكاشير')
        ->searchable(query: function (Builder $query, string $search): Builder {
          return $query->whereHas('cashier.user', function (Builder $q) use ($search) {
            $q->where('name', 'like', "%{$search}%");
          });
        }),
    ])
      ->filters([])
      ->defaultSort('created_at', 'DESC')
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make(),
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
      'index' => Pages\ListCashierSalesReturns::route('/'),
      'create' => Pages\CreateCashierSalesReturn::route('/create'),
      'edit' => Pages\EditCashierSalesReturn::route('/{record}/edit'),
    ];
  }

  public static function getEloquentQuery(): Builder
  {
    $query = parent::getEloquentQuery()->with(['variant.product', 'cashier.user', 'fatora']);
    $user = auth()->user();

    if ($user->hasRole('super_admin')) {
      return $query;
    }

    if ($user->hasRole('sales_point_manager')) {
      return $query->whereHas('cashier.salesPoint.managers', function (Builder $subQuery) use ($user) {
        $subQuery->where('user_id', $user->id);
      });
    }

    if ($user->hasRole('sales_point_cashier')) {
      $cashierId = SalesPointCashier::where('user_id', $user->id)->value('id');

      if (!$cashierId) {
        return $query->whereRaw('1 = 0');
      }

      return $query->where('sales_point_cashier_id', $cashierId);
    }

    return $query->whereRaw('1 = 0');
  }
}
