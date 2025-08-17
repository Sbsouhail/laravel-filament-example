<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\Day;
use App\Enums\ReserveAction;
use App\Filament\Resources\RestaurantResource\Pages;
use App\Models\Restaurant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use InvalidArgumentException;

class RestaurantResource extends Resource
{
    protected static ?string $model = Restaurant::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Businesses';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // --- Basic Details Section ---
                Forms\Components\Section::make('Basic Details')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('city_id')
                                    ->relationship('city', 'name'),
                                Forms\Components\Select::make('venue_id')
                                    ->relationship('venue', 'name'),
                                Forms\Components\Select::make('cuisine_id')
                                    ->relationship('cuisine', 'name'),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull(),
                    ]),

                // --- Settings Section ---
                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Textarea::make('exceptions')
                                    ->label('Exceptions')
                                    ->helperText('Enter one per line')
                                    ->rows(4)
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('coupon_per_user')
                                    ->required()
                                    ->numeric()
                                    ->default(3),
                                Forms\Components\Select::make('coupon_offer_id')
                                    ->label('Offer')
                                    ->relationship('coupon_offer', 'title'),
                                Forms\Components\Toggle::make('menu_from_file')
                                    ->default(false)
                                    ->columnSpanFull()
                                    ->live(),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true)
                                    ->columnSpanFull(),
                            ]),
                    ]),

                // --- Reservation Details ---
                Forms\Components\Section::make('Reservation Details')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('reserve_action')
                                    ->options(ReserveAction::class)
                                    ->reactive(),

                                Forms\Components\TextInput::make('website_url')
                                    ->maxLength(255)
                                    ->required(fn (Get $get) => $get('reserve_action') === ReserveAction::WEBSITE->value),

                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->required(fn (Get $get) => $get('reserve_action') === ReserveAction::CALL->value),
                                Forms\Components\Select::make('open_days')
                                    ->label('Open Days')
                                    ->multiple()
                                    ->options(Day::class)
                                    ->mutateDehydratedStateUsing(
                                        fn (?array $state): ?string => $state
                                            ? implode(',', array_map(
                                                function (mixed $day): string {
                                                    if ($day instanceof Day) {
                                                        return $day->value;
                                                    }

                                                    if (is_string($day)) {
                                                        return $day;
                                                    }

                                                    throw new InvalidArgumentException('Invalid type in open_days: expected Day or string.');
                                                },
                                                $state
                                            ))
                                            : null
                                    )
                                    ->formatStateUsing(
                                        fn (string|array|null $state): array => is_string($state)
                                            ? array_map(fn (string $day) => Day::from($day), explode(',', $state))
                                            : ($state ?? []),
                                    ),
                                Forms\Components\TimePicker::make('open_time')
                                    ->seconds(false),
                                Forms\Components\TimePicker::make('close_time')
                                    ->seconds(false),
                            ]),
                    ]),

                // --- Location & Links Section ---
                Forms\Components\Section::make('Location & Links')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Textarea::make('location'),
                                ...(config('services.google.maps.key') ? [
                                    Map::make('mapLocation')
                                        ->mapControls([
                                            'mapTypeControl' => true,
                                            'scaleControl' => true,
                                            'streetViewControl' => false,
                                            'rotateControl' => true,
                                            'fullscreenControl' => true,
                                            'searchBoxControl' => false,
                                            'zoomControl' => true,
                                        ])
                                        ->columnSpanFull()
                                        ->draggable()
                                        ->geolocate()
                                        ->defaultLocation([33.8937913, 35.5017767])
                                        ->autocomplete($fieldName = 'location', $types = ['geocode', 'establishment'])
                                        ->autocompleteReverse()
                                        ->clickable(fn (string $operation): bool => in_array($operation, ['create', 'edit'])),
                                ] : []),
                                Forms\Components\TextInput::make('instagram_url')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('menu_url')
                                    ->maxLength(255)
                                    ->visible(fn (Get $get): bool => (bool) ! $get('menu_from_file')),
                            ]),
                    ]),

                // --- Menu File Upload Section ---
                Forms\Components\Section::make('Menu File')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\SpatieMediaLibraryFileUpload::make('menu_file')
                                    ->collection('restaurant-menu')
                                    ->disk('s3')
                                    // ->visibility('private')
                                    ->openable()
                                    ->moveFiles()
                                    ->fetchFileInformation(false),
                            ]),
                    ])
                    ->visible(fn (Get $get): bool => (bool) $get('menu_from_file')),

                // --- Image Gallery Section ---
                Forms\Components\Section::make('Images')
                    ->schema([
                        Forms\Components\SpatieMediaLibraryFileUpload::make('images')
                            ->imageEditor()
                            ->panelLayout('grid')
                            ->columnSpanFull()
                            ->collection('restaurant-images')
                            ->image()
                            ->disk('s3')
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            // ->visibility('private')
                            // ->minFiles(1)
                            ->maxFiles(5)
                            ->moveFiles()
                            ->fetchFileInformation(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('city.name')
                    ->label('City')
                    ->sortable(),

                Tables\Columns\TextColumn::make('venue.name')
                    ->label('Venue')
                    ->sortable(),

                Tables\Columns\TextColumn::make('cuisine.name')
                    ->label('Cuisine')
                    ->sortable(),

                Tables\Columns\TextColumn::make('coupon_offer.title')
                    ->label('Offer')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('location')
                    ->label('Location')
                    ->limit(30)
                    ->searchable(),

                Tables\Columns\TextColumn::make('website_url')
                    ->label('Website')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('instagram_url')
                    ->label('Instagram')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('coupon_per_user')
                    ->label('Coupons/User')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('redemptions_count')
                    ->label('Redemptions')
                    ->sortable()
                    ->numeric(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount('redemptions'))
            ->filters([
                // Add filters if needed
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRestaurants::route('/'),
            'create' => Pages\CreateRestaurant::route('/create'),
            'view' => Pages\ViewRestaurant::route('/{record}'),
            'edit' => Pages\EditRestaurant::route('/{record}/edit'),
        ];
    }
}
