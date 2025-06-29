<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class AddressRelationManager extends RelationManager
{
    protected static string $relationship = 'address';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('first_name')
                    ->label('Nombres')
                    ->required()
                    ->maxLength(255),
                TextInput::make('last_name')
                    ->label('Apellidos')
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('Teléfono')
                    ->required()
                    ->tel()
                    ->maxLength(15),
                TextInput::make('street_address')
                    ->label('Dirección')
                    ->required()
                    ->maxLength(255),
                TextInput::make('city')
                    ->label('Ciudad')
                    ->required()
                    ->maxLength(255),
                TextInput::make('state')
                    ->label('Estado')
                    ->required()
                    ->maxLength(255),
                TextInput::make('zip_code')
                    ->label('Código Postal')
                    ->required()
                    ->numeric()
                    ->maxLength(255),
                Textarea::make('street_address')
                    ->label('Dirección Detallada')
                    ->maxLength(500)
                    ->required()
                    ->columnSpanFull()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('street_address')
            ->columns([
                TextColumn::make('fullname')
                    ->label('Nombres y Apellidos'),
                TextColumn::make('phone')
                    ->label('Teléfono'),
                TextColumn::make('city')
                    ->label('Ciudad'),
                TextColumn::make('state')
                    ->label('Estado'),
                TextColumn::make('zip_code')
                    ->label('Código Postal'),
                TextColumn::make('street_address')
                    ->label('Dirección')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
