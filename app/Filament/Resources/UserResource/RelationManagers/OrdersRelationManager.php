<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Filament\Resources\OrderResource;
use Filament\Forms;
use Filament\Tables;
use App\Models\Order;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
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
                    ->label('Método de Pago')
                    ->sortable(),
                TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failled' => 'danger',
                    })
                    ->label('Estado de Pago')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Fecha de Creación')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
                Action::make('Ver pedido')
                    ->url(fn (Order $record): string => OrderResource::getUrl('view', ['record' => $record->id]))
                    ->color('info')
                    ->icon('heroicon-o-eye'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
