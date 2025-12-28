<?php

namespace App\Filament\Resources\Plaza;

use App\Filament\Resources\Plaza\PlazaMembershipTeamResource\Pages;
use App\Models\Plaza\PlazaMembershipTeam;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PlazaMembershipTeamResource extends Resource
{
    protected static ?string $model = PlazaMembershipTeam::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $navigationLabel = 'Asignaciones Equipos';

    protected static ?string $modelLabel = 'Asignación de Equipo';

    protected static ?string $pluralModelLabel = 'Asignaciones de Equipos';

    protected static ?string $navigationGroup = 'Plaza';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Asignación de Equipo')
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
                        Forms\Components\Select::make('team_id')
                            ->label('Equipo')
                            ->relationship('team', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Toggle::make('is_team_leader')
                            ->label('Líder del Equipo')
                            ->default(false),
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
                Tables\Columns\TextColumn::make('membership.user.email')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('membership.store.name')
                    ->label('Tienda')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('team.name')
                    ->label('Equipo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_team_leader')
                    ->label('Líder')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assigned_at')
                    ->label('Asignado el')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('team_id')
                    ->label('Equipo')
                    ->relationship('team', 'name'),
                Tables\Filters\TernaryFilter::make('is_team_leader')
                    ->label('Líder del Equipo'),
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
            'index' => Pages\ListPlazaMembershipTeams::route('/'),
            'create' => Pages\CreatePlazaMembershipTeam::route('/create'),
            'edit' => Pages\EditPlazaMembershipTeam::route('/{record}/edit'),
        ];
    }
}

