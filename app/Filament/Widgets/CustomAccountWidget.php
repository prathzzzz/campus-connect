<?php

namespace App\Filament\Widgets;

use Filament\Widgets\AccountWidget as BaseAccountWidget;
use Illuminate\Support\Facades\Auth;

class CustomAccountWidget extends BaseAccountWidget
{
    public static function canView(): bool
    {
        return Auth::user()->hasRole(['admin', 'spoc', 'co-ordinator']);
    }
}
