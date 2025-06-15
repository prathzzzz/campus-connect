<?php

namespace App\Providers;

use App\Models\Department;
use App\Models\Division;
use App\Models\Student;
use App\Models\User;
use App\Policies\DepartmentPolicy;
use App\Policies\DivisionPolicy;
use App\Policies\StudentPolicy;
use App\Policies\UserPolicy;
use App\Policies\RolePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        Student::class => StudentPolicy::class,
        Department::class => DepartmentPolicy::class,
        Division::class => DivisionPolicy::class,
    ];

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
        //
    }
}
