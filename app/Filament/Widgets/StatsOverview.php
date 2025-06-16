<?php

namespace App\Filament\Widgets;

use App\Models\Department;
use App\Models\Division;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    public static function canView(): bool
    {
        return Auth::user()->can('view-admin-dashboard');
    }

    protected function getCards(): array
    {
        return [
            Stat::make('Total Departments', Department::count()),
            Stat::make('Total Divisions', Division::count()),
        ];
    }
}
