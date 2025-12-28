<?php

namespace App\Filament\Resources\Plaza;

use App\Filament\Resources\Plaza\PlazaRoleResource\Pages;
use App\Models\Plaza\PlazaRole;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PlazaRoleResource extends Resource
{
    protected static ?string $model = PlazaRole::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Roles';

    protected static ?string $modelLabel = 'Rol';

    protected static ?string $pluralModelLabel = 'Roles';

    protected static ?string $navigationGroup = 'Plaza';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true)
                    ->helperText('Identificador único del rol (ej: admin, editor)'),
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(50),
                Forms\Components\Textarea::make('description')
                    ->label('Descripción')
                    ->maxLength(255)
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_customizable')
                    ->label('Personalizable')
                    ->default(true)
                    ->helperText('Permite personalizar capacidades para este rol'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_customizable')
                    ->label('Personalizable')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('memberships_count')
                    ->label('Miembros')
                    ->counts('memberships')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_customizable')
                    ->label('Personalizable'),
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
            'index' => Pages\ListPlazaRoles::route('/'),
            'create' => Pages\CreatePlazaRole::route('/create'),
            'edit' => Pages\EditPlazaRole::route('/{record}/edit'),
        ];
    }
}

