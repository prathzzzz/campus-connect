<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        $user = Auth::user();

        if ($user->hasAnyRole(['spoc', 'co-ordinator'])) {
            return new RedirectResponse(route('filament.admin.pages.dashboard'));
        }

        if ($user->hasRole('student')) {
            // We'll assume a student dashboard route exists.
            // We can create this route and view later.
            return new RedirectResponse('/student/dashboard');
        }

        // Default redirect for any other roles or users without specific roles
        return new RedirectResponse('/home');
    }
}
