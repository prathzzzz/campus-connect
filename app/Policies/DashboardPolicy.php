<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DashboardPolicy
{
  use HandlesAuthorization;

  public static array $permissions = [
    'view-admin-dashboard',
  ];

  public function viewAdminDashboard(User $user): bool
  {
    return $user->hasPermissionTo('view-admin-dashboard');
  }
}
