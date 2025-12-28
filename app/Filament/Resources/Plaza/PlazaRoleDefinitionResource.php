<?php

namespace App\Filament\Resources\Plaza;

use App\Filament\Resources\Plaza\PlazaRoleDefinitionResource\Pages;
use App\Models\Plaza\PlazaRoleDefinition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PlazaRoleDefinitionResource extends Resource
{
    protected static ?string $model = PlazaRoleDefinition::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Definiciones de Rol';

    protected static ?string $modelLabel = 'Definición de Rol';

    protected static ?string $pluralModelLabel = 'Definiciones de Rol';

    protected static ?string $navigationGroup = 'Plaza';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Definición de Rol')
                    ->schema([
                        Forms\Components\Select::make('role_id')
                            ->label('Rol')
                            ->relationship('role', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('capability_id')
                            ->label('Capacidad')
                            ->relationship('capability', 'label')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('role.name')
                    ->label('Rol')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('capability.module')
                    ->label('Módulo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('capability.slug')
                    ->label('Slug Capacidad')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('capability.label')
                    ->label('Capacidad')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role_id')
                    ->label('Rol')
                    ->relationship('role', 'name'),
                Tables\Filters\SelectFilter::make('capability_id')
                    ->label('Capacidad')
                    ->relationship('capability', 'label'),
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
            'index' => Pages\ListPlazaRoleDefinitions::route('/'),
            'create' => Pages\CreatePlazaRoleDefinition::route('/create'),
            'edit' => Pages\EditPlazaRoleDefinition::route('/{record}/edit'),
        ];
    }
}

