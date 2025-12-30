<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Filament\Navigation\NavigationGroup;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
                'gray' => Color::Slate,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            // ->widgets([
            //     \App\Filament\Widgets\StatsOverview::class,
            // ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Administración')
                    ->collapsed(false),
                NavigationGroup::make()
                    ->label('Configuración')
                    ->collapsed(),
            ])
            // ->plugins([
            //     \Filament\SpatieLaravelTranslatablePlugin::make()
            //         ->defaultLocales(['es']),
            //     \Filament\LanguageSwitch\LanguageSwitchPlugin::make()
            //         ->visible(fn() => auth()->user()?->can('view language-switcher'))
            // ])
        ;
    }
}
