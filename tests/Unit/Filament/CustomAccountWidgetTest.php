<?php

namespace Tests\Unit\Filament;

use App\Filament\Widgets\CustomAccountWidget;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CustomAccountWidgetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @dataProvider permissionProvider
     */
    public function it_correctly_implements_can_view($roleName, $canView)
    {
        $role = Role::create(['name' => $roleName]);
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user);

        $this->assertEquals($canView, CustomAccountWidget::canView());
    }

    public static function permissionProvider()
    {
        return [
            'admin can view' => ['admin', true],
            'spoc can view' => ['spoc', true],
            'co-ordinator can view' => ['co-ordinator', true],
            'student cannot view' => ['student', false],
            'other role cannot view' => ['other', false],
        ];
    }
}