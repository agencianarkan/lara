<?php

namespace App\Filament\Resources\Plaza;

use App\Filament\Resources\Plaza\PlazaStoreResource\Pages;
use App\Models\Plaza\PlazaStore;
use App\Models\Plaza\PlazaUser;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PlazaStoreResource extends Resource
{
    protected static ?string $model = PlazaStore::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationLabel = 'Tiendas';

    protected static ?string $modelLabel = 'Tienda';

    protected static ?string $pluralModelLabel = 'Tiendas';

    protected static ?string $navigationGroup = 'Plaza';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('domain_url')
                            ->label('URL del Dominio')
                            ->required()
                            ->url()
                            ->maxLength(150)
                            ->helperText('URL completa del dominio de la tienda'),
                        Forms\Components\Select::make('platform_type')
                            ->label('Tipo de Plataforma')
                            ->options([
                                'woocommerce' => 'WooCommerce',
                                'shopify' => 'Shopify',
                                'jumpseller' => 'Jumpseller',
                                'custom' => 'Personalizada',
                            ])
                            ->required()
                            ->default('woocommerce'),
                        Forms\Components\TextInput::make('plaza_api_key')
                            ->label('API Key de Plaza')
                            ->required()
                            ->maxLength(64)
                            ->unique(ignoreRecord: true)
                            ->helperText('Clave API única para esta tienda'),
                        Forms\Components\TextInput::make('logo_url')
                            ->label('URL del Logo')
                            ->url()
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Propietario')
                    ->schema([
                        Forms\Components\Select::make('owner_id')
                            ->label('Propietario')
                            ->relationship('owner', 'email')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('email')
                                    ->label('Correo')
                                    ->email()
                                    ->required(),
                                Forms\Components\TextInput::make('first_name')
                                    ->label('Nombre')
                                    ->required(),
                                Forms\Components\TextInput::make('last_name')
                                    ->label('Apellido')
                                    ->required(),
                            ]),
                    ]),
                Forms\Components\Section::make('Configuración de Conexión')
                    ->schema([
                        Forms\Components\Textarea::make('connection_config')
                            ->label('Configuración JSON')
                            ->json()
                            ->helperText('Configuración de conexión en formato JSON')
                            ->columnSpanFull()
                            ->rows(5),
                    ])
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
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('domain_url')
                    ->label('Dominio')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('platform_type')
                    ->label('Plataforma')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('owner.email')
                    ->label('Propietario')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('platform_type')
                    ->label('Plataforma')
                    ->options([
                        'woocommerce' => 'WooCommerce',
                        'shopify' => 'Shopify',
                        'jumpseller' => 'Jumpseller',
                        'custom' => 'Personalizada',
                    ]),
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
            'index' => Pages\ListPlazaStores::route('/'),
            'create' => Pages\CreatePlazaStore::route('/create'),
            'edit' => Pages\EditPlazaStore::route('/{record}/edit'),
        ];
    }
}

