<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Student;
use App\Models\Department;
use App\Models\Division;

class StatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Stat::make('Total Students', Student::count()),
            Stat::make('Total Departments', Department::count()),
            Stat::make('Total Divisions', Division::count()),
        ];
    }
}
