<?php

namespace App\Filament\Resources\Plaza;

use App\Filament\Resources\Plaza\PlazaMembershipResource\Pages;
use App\Models\Plaza\PlazaMembership;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PlazaMembershipResource extends Resource
{
    protected static ?string $model = PlazaMembership::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationLabel = 'Membresías';

    protected static ?string $modelLabel = 'Membresía';

    protected static ?string $pluralModelLabel = 'Membresías';

    protected static ?string $navigationGroup = 'Plaza';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de Membresía')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Usuario')
                            ->relationship('user', 'email')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('store_id')
                            ->label('Tienda')
                            ->relationship('store', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('role_id')
                            ->label('Rol')
                            ->relationship('role', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Toggle::make('is_custom_mode')
                            ->label('Modo Personalizado')
                            ->default(false)
                            ->helperText('Permite personalizar capacidades individuales'),
                        Forms\Components\Select::make('invited_by')
                            ->label('Invitado Por')
                            ->relationship('invitedBy', 'email')
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('store.name')
                    ->label('Tienda')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role.name')
                    ->label('Rol')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_custom_mode')
                    ->label('Personalizado')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('invitedBy.email')
                    ->label('Invitado Por')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('store_id')
                    ->label('Tienda')
                    ->relationship('store', 'name'),
                Tables\Filters\SelectFilter::make('role_id')
                    ->label('Rol')
                    ->relationship('role', 'name'),
                Tables\Filters\TernaryFilter::make('is_custom_mode')
                    ->label('Modo Personalizado'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListPlazaMemberships::route('/'),
            'create' => Pages\CreatePlazaMembership::route('/create'),
            'edit' => Pages\EditPlazaMembership::route('/{record}/edit'),
        ];
    }
}

