<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductResource\RelationManagers;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $titlePanel = 'Productos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Group::make()->schema([
                    Section::make('Información de Producto')->schema([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function(string $operation, $state, Set $set){
                                if($operation !== 'create'){
                                    return;
                                }
                                $set('slug', Str::slug($state));
                            }),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated()
                            ->unique(Product::class, 'slug', ignoreRecord: true),

                        MarkdownEditor::make('description')
                            ->label('Descripción')
                            ->columnSpanFull()
                            ->fileAttachmentsDirectory('products')
                    ])->columns(2),

                    Section::make('Images')->schema([
                        FileUpload::make('images')
                        ->label('Imágenes')
                        ->multiple()
                        ->directory('products')
                        ->maxFiles(5)
                        ->reorderable()
                    ])
                ])->columnSpan(2),

                Group::make()->schema([
                    Section::make('Precio')->schema([
                        TextInput::make('price')
                            ->label('Precio')
                            ->numeric()
                            ->required()
                            ->prefix('PEN')
                    ]),
                    Section::make('Relaciones')->schema([
                        Select::make('category_id')
                            ->label('Categoria')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->relationship('category','name'),
                        Select::make('brand_id')
                            ->label('Marca')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->relationship('brand','name'),
                    ]),
                    Section::make('Status')->schema([
                        Toggle::make('is_stock')
                            ->label('En stock')
                            ->required()
                            ->default(true),
                        Toggle::make('is_active')
                            ->label('Esta activo')
                            ->required()
                            ->default(true),
                        Toggle::make('is_featured')
                            ->label('Es destaca')
                            ->required(),
                        Toggle::make('on_sale')
                            ->label('En venta')
                            ->required()
                    ])
                ])->columnSpan(1)
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('name')
                ->label('Nombre')
                ->searchable(),
                TextColumn::make('category.name')
                ->label('Categoria')
                ->searchable(),
                TextColumn::make('brand.name')
                ->label('Marca')
                ->searchable(),
                TextColumn::make('price')
                ->label('Precio')
                ->money('PEN')
                ->sortable(),
                IconColumn::make('is_featured')
                ->label('Destacado')
                ->boolean(),
                IconColumn::make('on_sale')
                ->label('En venta')
                ->boolean(),
                IconColumn::make('is_stock')
                ->label('En stock')
                ->boolean(),
                IconColumn::make('is_active')
                ->label('Esta activo')
                ->boolean(),

                TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name'),
                SelectFilter::make('brand')
                    ->relationship('brand', 'name')
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    ViewAction::make(),
                    DeleteAction::make(),
                ])
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return static::$titlePanel;
    }
}
