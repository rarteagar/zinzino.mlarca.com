<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BasePage;

class Dashboard extends BasePage
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?int $navigationSort = 1;

    public function getHeading(): string
    {
        return 'Panel de Administración';
    }

    public function getSubheading(): ?string
    {
        return 'Bienvenido al panel de administración';
    }
}
