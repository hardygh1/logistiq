<?php

namespace App\Filament\Resources\OrderResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\OrderResource\Widgets\OrderStats;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Crear Pedido'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OrderStats::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('All')->label('Todos'),
            'nuevo' => Tab::make('Nuevo')->query(fn ($query) => $query->where('status', 'nuevo')),
            'en_proceso' => Tab::make('En Proceso')->query(fn ($query) => $query->where('status', 'en_proceso')),
            'enviado' => Tab::make('Enviado')->query(fn ($query) => $query->where('status', 'enviado')),
            'entregado' => Tab::make('Entregado')->query(fn ($query) => $query->where('status', 'entregado')),
            'cancelado' => Tab::make('Cancelado')->query(fn ($query) => $query->where('status', 'cancelado'))
        ];
    }

}
