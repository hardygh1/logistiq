<?php

namespace App\Filament\Resources;

use Dom\Text;
use Filament\Tables;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Number;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\ToggleButtons;
use App\Filament\Resources\OrderResource\Pages;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $titlePanel = 'Pedidos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Order Information')->schema([
                        Select::make('user_id')
                        ->label('Customer')
                        ->relationship('user','name')
                        ->searchable()
                        ->preload()
                        ->required(),
                        Select::make('payment_method')
                        ->options([
                            'stripe'=> 'Stripe',
                            'cod'=>'Cash on Delivery'
                        ])->required(),
                        Select::make('payment_status')
                            ->options([
                                'pending' => 'Pendiente',
                                'paid' => 'Pagado',
                                'failled' => 'Fallido'
                            ])
                            ->default('pending')
                            ->required(),
                        ToggleButtons::make('status')
                            ->inline()
                            ->default('nuevo')
                            ->required()
                            ->options([
                                'nuevo'=> 'Nuevo',
                                'procesando' => 'Procesando',
                                'enviado'=>'Enviado',
                                'entregado'=>'Entregado',
                                'cancelado'=>'Cancelado'
                            ])
                            ->colors([
                                'nuevo'=> 'info',
                                'procesando' => 'warning',
                                'enviado'=>'info',
                                'entregado'=>'success',
                                'cancelado'=>'danger'
                            ])
                            ->icons([
                                'nuevo' => 'heroicon-m-sparkles',
                                'procesando' => 'heroicon-m-arrow-path',
                                'enviado'=>'heroicon-m-truck',
                                'entregado'=>'heroicon-m-check-badge',
                                'cancelado'=>'heroicon-m-x-circle'
                            ]),
                        Select::make('currency')
                            ->options([
                                'PEN' => 'PEN - Soles'
                            ])
                            ->default('PEN')
                            ->required(),
                        Select::make('shipping_method')
                            ->options([
                                'shalom'=>'Shalom',
                                'dhl'=>'DHL',
                                'tren'=> 'Tren'
                            ]),
                        Textarea::make('notes')
                        ->columnSpanFull()
                    ])->columns(2),

                    Section::make('Order Items')->schema([
                        Repeater::make('items')
                        ->relationship()
                        ->schema([

                            Select::make('product_id')
                                ->relationship('product', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->distinct()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                ->reactive()
                                ->afterStateUpdated(fn ($state, Set $set) => $set('unit_amount', Product::find($state)?->price ?? 0))
                                ->afterStateUpdated(fn ($state, Set $set) => $set('total_amount', Product::find($state)?->price ?? 0))
                                ->columnSpan(4),
                            TextInput::make('quantity')
                                ->label('Cantidad')
                                ->numeric()
                                ->required()
                                ->default(1)
                                ->minValue(1)
                                ->reactive()
                                ->afterStateUpdated(fn ($state, Set $set, Get $get) => $set('total_amount', $state * $get('unit_amount')))
                                ->columnSpan(2),
                            TextInput::make('unit_amount')
                                ->label('Precio Unitario')
                                ->numeric()
                                ->required()
                                ->disabled()
                                ->dehydrated()
                                ->columnSpan(3),
                            TextInput::make('total_amount')
                                ->label('Precio Total')
                                ->numeric()
                                ->required()
                                ->dehydrated()
                                ->columnSpan(3),

                        ])->columns(12),

                        Placeholder::make('grand_total_placeholder')
                            ->label('Total')
                            ->content(function (Get $get , Set $set){
                                $total = 0;
                                if(!$repeaters = $get('items')){
                                    return $total;
                                }

                                foreach($repeaters as $key => $repeaters){
                                    $total += $get("items.{$key}.total_amount");
                                }
                                $set('grand_total',$total);
                                return Number::currency($total, 'PEN');
                            }),
                        Hidden:: make('grand_total')
                            ->default(0)
                    ])
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                ->label('Codigo')
                ->formatStateUsing(fn ($state) => 'PED-' . str_pad($state, 4, '0', STR_PAD_LEFT))
                ->searchable(),
                TextColumn::make('user.name')
                ->label('Usuario')
                ->searchable(),
                TextColumn::make('grand_total')
                ->label('Total de Pedido')
                ->money('PEN')
                ->sortable(),
                TextColumn::make('shipping_method')
                ->label('Metodo de Envio')
                ->searchable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return static::$titlePanel;
    }
}
