<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Columns\TextColumn;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /** Register any application services. */
    public function register(): void
    {

    }

    /** Bootstrap any application services. */
    public function boot(): void
    {
        Gate::define('viewApiDocs', function (User $user) {
            return $user->is_admin;
        });

        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                /** @var SecurityScheme */
                $bearerSchema = SecurityScheme::http('bearer');
                $openApi->secure(
                    $bearerSchema
                );
            });

        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_START,
            fn (): string => Blade::render('@livewire(\'timezone-listener\')'),
        );

        // Set default timezone for DateTimePicker
        DateTimePicker::configureUsing(function (DateTimePicker $component): void {
            /** @var string */
            $defaultTimezone = session('user_timezone') ?? config('app.timezone') ?? 'UTC';
            $component->timezone($defaultTimezone);
        });

        // Set default timezone for TimePicker
        TimePicker::configureUsing(function (DateTimePicker $component): void {
            /** @var string */
            $defaultTimezone = session('user_timezone') ?? config('app.timezone') ?? 'UTC';
            $component->timezone($defaultTimezone);
        });

        // Set default timezone for TextColumn for datetime attibutes
        TextColumn::configureUsing(function (TextColumn $component): void {
            /** @var string */
            $defaultTimezone = session('user_timezone') ?? config('app.timezone') ?? 'UTC';
            if (in_array($component->getName(), ['created_at', 'updated_at', 'published_at', 'email_verified_at'])) {
                $component->timezone($defaultTimezone);
            }
        });
    }
}
