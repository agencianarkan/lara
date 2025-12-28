<?php

namespace App\Filament\Resources\Plaza;

use App\Filament\Resources\Plaza\PlazaCustomOverrideResource\Pages;
use App\Models\Plaza\PlazaCustomOverride;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PlazaCustomOverrideResource extends Resource
{
    protected static ?string $model = PlazaCustomOverride::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?string $navigationLabel = 'Sobrescrituras Personalizadas';

    protected static ?string $modelLabel = 'Sobrescritura Personalizada';

    protected static ?string $pluralModelLabel = 'Sobrescrituras Personalizadas';

    protected static ?string $navigationGroup = 'Plaza';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Sobrescritura Personalizada')
                    ->schema([
                        Forms\Components\Select::make('membership_id')
                            ->label('Membresía')
                            ->relationship('membership', 'id', fn ($query) => $query->with(['user', 'store']))
                            ->getOptionLabelFromRecordUsing(fn ($record) => 
                                "{$record->user->email} - {$record->store->name}"
                            )
                            ->searchable(['user.email', 'store.name'])
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('capability_id')
                            ->label('Capacidad')
                            ->relationship('capability', 'label')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Toggle::make('is_granted')
                            ->label('Concedida')
                            ->required()
                            ->helperText('Si está activado, la capacidad está concedida. Si está desactivado, está denegada.'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('membership.user.email')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('membership.store.name')
                    ->label('Tienda')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('capability.module')
                    ->label('Módulo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('capability.label')
                    ->label('Capacidad')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_granted')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('membership_id')
                    ->label('Membresía')
                    ->relationship('membership', 'id'),
                Tables\Filters\SelectFilter::make('capability_id')
                    ->label('Capacidad')
                    ->relationship('capability', 'label'),
                Tables\Filters\TernaryFilter::make('is_granted')
                    ->label('Concedida'),
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
            'index' => Pages\ListPlazaCustomOverrides::route('/'),
            'create' => Pages\CreatePlazaCustomOverride::route('/create'),
            'edit' => Pages\EditPlazaCustomOverride::route('/{record}/edit'),
        ];
    }
}

