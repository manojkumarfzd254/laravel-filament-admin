<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;
use Illuminate\Support\ServiceProvider;

class CustomFilamentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Filament::serving(function () {
            Filament::registerUserMenuItems([
                // 'account' => UserMenuItem::make()
                //     ->label('Edit Profile')
                //     ->url(route('filament.admin.pages.edit-profile')) // Adjust this URL as per your route setup
                //     ->icon('heroicon-o-user'),
                'Profile' => UserMenuItem::make()
                    ->label('Edit Profile')
                    ->url(route('filament.admin.pages.edit-profile')) // Adjust this URL as per your route setup
                    ->icon('heroicon-o-user'),

                // Optional: add other items or adjust visibility as needed
            ]);
        });
    }
}
