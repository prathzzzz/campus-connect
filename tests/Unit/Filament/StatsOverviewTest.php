<?php

namespace Tests\Unit\Filament;

use App\Filament\Widgets\StatsOverview;
use App\Models\Department;
use App\Models\Division;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use ReflectionMethod;

class StatsOverviewTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_the_correct_cards_with_counts()
    {
        Role::create(['name' => 'student']);
        $departments = Department::factory()->count(3)->create();
        $divisions = Division::factory()->count(2)->create([
            'department_id' => $departments->first()->id,
        ]);
        Student::factory()->count(5)->create([
            'department_id' => $departments->first()->id,
            'division_id' => $divisions->first()->id,
        ]);

        $widget = new StatsOverview();

        $method = new ReflectionMethod(StatsOverview::class, 'getCards');
        $method->setAccessible(true);
        $cards = $method->invoke($widget);

        $this->assertCount(3, $cards);

        $this->assertEquals('Total Students', $cards[0]->getLabel());
        $this->assertEquals(5, $cards[0]->getValue());

        $this->assertEquals('Total Departments', $cards[1]->getLabel());
        $this->assertEquals(3, $cards[1]->getValue());

        $this->assertEquals('Total Divisions', $cards[2]->getLabel());
        $this->assertEquals(2, $cards[2]->getValue());
    }

    /**
     * @test
     * @dataProvider permissionProvider
     */
    public function it_correctly_implements_can_view($roleName, $canView)
    {
        $role = Role::create(['name' => $roleName]);
        /** @var \App\Models\User $user */
        $user = \App\Models\User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user);

        $this->assertEquals($canView, StatsOverview::canView());
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