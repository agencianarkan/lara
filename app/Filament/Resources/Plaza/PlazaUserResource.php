<?php

namespace App\Filament\Resources\Plaza;

use App\Filament\Resources\Plaza\PlazaUserResource\Pages;
use App\Models\Plaza\PlazaUser;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class PlazaUserResource extends Resource
{
    protected static ?string $model = PlazaUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'Usuarios Plaza';

    protected static ?string $modelLabel = 'Usuario Plaza';

    protected static ?string $pluralModelLabel = 'Usuarios Plaza';

    protected static ?string $navigationGroup = 'Plaza';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Personal')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required()
                            ->maxLength(150)
                            ->unique(ignoreRecord: true)
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('password_hash')
                            ->label('Contraseña')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->helperText('Dejar vacío para mantener la contraseña actual')
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('first_name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(50),
                        Forms\Components\TextInput::make('last_name')
                            ->label('Apellido')
                            ->required()
                            ->maxLength(50),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Estado y Permisos')
                    ->schema([
                        Forms\Components\Toggle::make('is_platform_admin')
                            ->label('Administrador de Plataforma')
                            ->default(false),
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'pending' => 'Pendiente',
                                'active' => 'Activo',
                                'suspended' => 'Suspendido',
                            ])
                            ->required()
                            ->default('pending'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Tokens y Seguridad')
                    ->schema([
                        Forms\Components\TextInput::make('verification_token')
                            ->label('Token de Verificación')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('reset_token')
                            ->label('Token de Reset')
                            ->maxLength(100),
                        Forms\Components\DateTimePicker::make('token_expires_at')
                            ->label('Token Expira'),
                        Forms\Components\TextInput::make('failed_login_attempts')
                            ->label('Intentos Fallidos')
                            ->numeric()
                            ->default(0),
                        Forms\Components\DateTimePicker::make('lockout_until')
                            ->label('Bloqueado Hasta'),
                        Forms\Components\DateTimePicker::make('last_login_at')
                            ->label('Último Acceso'),
                    ])
                    ->columns(3)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->label('Apellido')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_platform_admin')
                    ->label('Admin')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'pending' => 'warning',
                        'suspended' => 'danger',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Último Acceso')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_platform_admin')
                    ->label('Administrador'),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'active' => 'Activo',
                        'suspended' => 'Suspendido',
                    ]),
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
            'index' => Pages\ListPlazaUsers::route('/'),
            'create' => Pages\CreatePlazaUser::route('/create'),
            'edit' => Pages\EditPlazaUser::route('/{record}/edit'),
        ];
    }
}

