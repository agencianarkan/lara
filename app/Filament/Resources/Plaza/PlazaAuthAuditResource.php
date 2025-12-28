<?php

namespace App\Filament\Resources\Plaza;

use App\Filament\Resources\Plaza\PlazaAuthAuditResource\Pages;
use App\Models\Plaza\PlazaAuthAudit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PlazaAuthAuditResource extends Resource
{
    protected static ?string $model = PlazaAuthAudit::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static ?string $navigationLabel = 'Auditoría de Autenticación';

    protected static ?string $modelLabel = 'Registro de Auditoría';

    protected static ?string $pluralModelLabel = 'Registros de Auditoría';

    protected static ?string $navigationGroup = 'Plaza';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de Auditoría')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Usuario')
                            ->relationship('user', 'email')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('event_type')
                            ->label('Tipo de Evento')
                            ->required()
                            ->maxLength(50),
                        Forms\Components\TextInput::make('ip_address')
                            ->label('Dirección IP')
                            ->required()
                            ->maxLength(45),
                        Forms\Components\TextInput::make('user_agent')
                            ->label('User Agent')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('metadata')
                            ->label('Metadatos JSON')
                            ->json()
                            ->helperText('Metadatos adicionales en formato JSON')
                            ->columnSpanFull()
                            ->rows(5),
                    ])
                    ->columns(2)
                    ->disabled(fn ($context) => $context === 'view'),
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
                    ->sortable()
                    ->default('N/A'),
                Tables\Columns\TextColumn::make('event_type')
                    ->label('Tipo de Evento')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_agent')
                    ->label('User Agent')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->user_agent)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Usuario')
                    ->relationship('user', 'email'),
                Tables\Filters\Filter::make('event_type')
                    ->form([
                        Forms\Components\TextInput::make('event_type')
                            ->label('Tipo de Evento'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when(
                            $data['event_type'],
                            fn ($query, $eventType) => $query->where('event_type', 'like', "%{$eventType}%")
                        );
                    }),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn ($query, $date) => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn ($query, $date) => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->bulkActions([
                // Sin acciones en bulk para auditoría
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
            'index' => Pages\ListPlazaAuthAudits::route('/'),
            'view' => Pages\ViewPlazaAuthAudit::route('/{record}'),
        ];
    }
}

