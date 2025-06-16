<?php

namespace Tests\Unit\Filament;

use App\Filament\Widgets\StatsOverview;
use App\Models\Department;
use App\Models\Division;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;
use ReflectionMethod;
use Tests\TestCase;

class StatsOverviewTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('app:sync-permissions');
    }

    #[Test]
    public function it_returns_the_correct_cards_with_counts()
    {
        $departments = Department::factory()->count(3)->create();
        Division::factory()->count(2)->create([
            'department_id' => $departments->first()->id,
        ]);

        $widget = new StatsOverview();
        $method = new ReflectionMethod(StatsOverview::class, 'getCards');
        $cards = $method->invoke($widget);

        $this->assertCount(2, $cards);

        $this->assertEquals('Total Departments', $cards[0]->getLabel());
        $this->assertEquals(3, $cards[0]->getValue());

        $this->assertEquals('Total Divisions', $cards[1]->getLabel());
        $this->assertEquals(2, $cards[1]->getValue());
    }

    #[Test]
    public function admin_can_view_stats_overview()
    {
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);
        $this->assertTrue(StatsOverview::canView());
    }

    #[Test]
    public function non_admin_cannot_view_stats_overview()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->assertFalse(StatsOverview::canView());
    }
}
