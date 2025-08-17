<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\Gender;
use App\Filament\Exports\UserExporter;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // --- Authentication Section ---
                Forms\Components\Section::make('Authentication')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('password')
                                    ->password()
                                    ->autocomplete('new-password')
                                    ->dehydrateStateUsing(fn (?string $state) => filled($state) ? $state : null)
                                    ->dehydrated(fn (?string $state) => filled($state))
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->visible(fn (User|null $record, string $operation): bool => $operation === 'create' || ($operation === 'edit' && $record?->id === Auth::id()))
                                    ->maxLength(255),
                            ]),
                    ]),

                // --- Personal Information Section ---
                Forms\Components\Section::make('Personal Information')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('first_name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('last_name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('gender')
                                    ->required()
                                    ->options(Gender::class),
                                Forms\Components\DatePicker::make('date_of_birth'),
                            ]),
                    ]),

                // --- Account Settings Section ---
                Forms\Components\Section::make('Account Settings')
                    ->schema([
                        Forms\Components\SpatieMediaLibraryFileUpload::make('avatar')
                            ->collection('user-avatar')
                            ->disk('s3')
                            // ->visibility('private')
                            ->openable()
                            ->moveFiles()
                            ->image()
                            ->fetchFileInformation(false),
                        Forms\Components\TextInput::make('invite_limit')
                            ->required()
                            ->numeric()
                            ->minValue(fn (User|null $record): int => $record?->inviteCodesCount() ?: 0)
                            ->default(3),
                        Forms\Components\Toggle::make('is_admin')
                            ->label('Admin')
                            ->disabled(fn (User|null $record): bool => $record?->id === Auth::id())
                            ->default(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // SpatieMediaLibraryImageColumn::make('avatar')
                //     ->toggleable()
                //     ->collection('user-avatar')
                //     ->conversion('preview')
                //     ->disk('s3')
                //     ->visibility('private'),

                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),

                Tables\Columns\TextColumn::make('gender')
                    ->searchable(),

                Tables\Columns\TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_admin')
                    ->boolean()
                    ->toggleable()
                    ->toggledHiddenByDefault(),

                Tables\Columns\TextColumn::make('invite_limit')
                    ->numeric()
                    ->sortable(),

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
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount('redemptions'))
            ->filters([
                // Optional filters
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(UserExporter::class)
                    ->modifyQueryUsing(
                        fn (Builder $query) => $query->withCount('redemptions')
                            ->with('redemptions.restaurant')
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\EditAction::make()
                    ->visible(fn (User $record) => $record->id === Auth::id() || ! $record->is_admin),
            ]);
    }

    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
