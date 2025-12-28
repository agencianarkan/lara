<?php

namespace App\Filament\Resources\Plaza;

use App\Filament\Resources\Plaza\PlazaCapabilityResource\Pages;
use App\Models\Plaza\PlazaCapability;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PlazaCapabilityResource extends Resource
{
    protected static ?string $model = PlazaCapability::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationLabel = 'Capacidades';

    protected static ?string $modelLabel = 'Capacidad';

    protected static ?string $pluralModelLabel = 'Capacidades';

    protected static ?string $navigationGroup = 'Plaza';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('module')
                    ->label('Módulo')
                    ->required()
                    ->maxLength(30)
                    ->helperText('Módulo al que pertenece esta capacidad (ej: stores, orders)'),
                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true)
                    ->helperText('Identificador único de la capacidad'),
                Forms\Components\TextInput::make('label')
                    ->label('Etiqueta')
                    ->required()
                    ->maxLength(100)
                    ->helperText('Nombre descriptivo de la capacidad'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('module')
                    ->label('Módulo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('label')
                    ->label('Etiqueta')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('module')
                    ->label('Módulo')
                    ->options(function () {
                        return PlazaCapability::query()
                            ->distinct()
                            ->pluck('module', 'module')
                            ->toArray();
                    }),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlazaCapabilities::route('/'),
            'create' => Pages\CreatePlazaCapability::route('/create'),
            'edit' => Pages\EditPlazaCapability::route('/{record}/edit'),
        ];
    }
}

