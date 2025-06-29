<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Order;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

use App\Filament\Resources\OrderResource;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    protected int |string | array $columnSpan = 'full';
    protected static ?int $sort = 1;
    protected static ?string $heading = 'Últimos pedidos';

    public function table(Table $table): Table
    {
        return $table
            ->query(OrderResource::getEloquentQuery())
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                ->label('Código')
                ->formatStateUsing(fn ($state) => 'PED-' . str_pad($state, 4, '0', STR_PAD_LEFT))
                ->searchable()
                ->sortable(),
                TextColumn::make('grand_total')
                    ->label('Total')
                    ->money('PEN')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'nuevo' => 'primary',
                        'en_proceso' => 'warning',
                        'enviado' => 'info',
                        'entregado' => 'success',
                        'cancelado' => 'danger',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'nuevo' => 'heroicon-m-sparkles',
                        'procesando' => 'heroicon-m-arrow-path',
                        'enviado'=>'heroicon-m-truck',
                        'entregado'=>'heroicon-m-check-badge',
                        'cancelado'=>'heroicon-m-x-circle'
                    })
                    ->label('Estado')
                    ->sortable(),

                TextColumn::make('payment_method')
                ->badge()
                ->formatStateUsing(fn ($state) => match ($state) {
                    'cash' => 'Efectivo',
                    'yape' => 'Yape',
                    'plin' => 'Plin',
                    'transfer' => 'Transferencia',
                    default => ucfirst($state),
                })
                ->extraAttributes(fn ($state) => match ($state) {
                    'yape' => ['class' => 'bg-purple-500 text-white'],
                    'cash' => ['class' => 'bg-green-500 text-white'],
                    'plin' => ['class' => 'bg-blue-400 text-white'],
                    'transfer' => ['class' => 'bg-yellow-400 text-black'],
                    default => ['class' => 'bg-gray-300 text-black'],
                }),

                TextColumn::make('payment_status')
                    ->badge()
                    ->label('Estado de Pago')
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente' => 'warning',
                        'pagado' => 'success',
                        'fallido' => 'danger',
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Fecha de Creación')
                    ->sortable(),
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
                Action::make('Ver pedido')
                    ->url(fn (Order $record): string => OrderResource::getUrl('view', ['record' => $record->id]))
                    ->color('primary')
                    ->icon('heroicon-o-eye'),
            ]);
    }
}
