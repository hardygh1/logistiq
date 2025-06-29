<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;
use Illuminate\Support\Number;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class OrderStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Nuevos Pedidos', Order::query()->where('status', 'nuevo')->count()),
            Stat::make('En Proceso', Order::query()->where('status', 'en_proceso')->count()),
            Stat::make('Completados', Order::query()->where('status', 'completado')->count()),
            Stat::make('Precio Promedio', Number::currency(Order::query()->avg('grand_total'),'PEN')),
            Stat::make('Total Pedidos', Order::count())
                ->description(Order::sum('grand_total'))
                ->descriptionColor('success')
                ->icon('heroicon-o-shopping-cart')
                ->chart(Order::query()->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->pluck('count', 'date')->toArray(),'PEN')
                ->chartColor('blue.500'),
        ];
    }
}
