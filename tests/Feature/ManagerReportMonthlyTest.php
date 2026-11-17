<?php

namespace Tests\Feature;

use App\Models\ManagerReport;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ManagerReportMonthlyTest extends TestCase
{
    use RefreshDatabase;

    protected function manager(): User
    {
        Role::findOrCreate('manager', 'web');
        Role::findOrCreate('admin', 'web');

        $user = User::factory()->create();
        $user->assignRole('manager');

        return $user;
    }

    public function test_monthly_report_persists_with_month_and_year(): void
    {
        $this->actingAs($this->manager());

        $response = $this->post(route('manager.reports.store'), [
            'type' => 'monthly',
            'month' => 10,
            'year' => 2026,
            'revenue_overall' => 150000,
            'staff_performance' => 'All good',
            'manager_signature' => 'Test Manager',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('manager_reports', [
            'type' => 'monthly',
            'month' => 10,
            'year' => 2026,
            'revenue_overall' => 150000,
        ]);

        $report = ManagerReport::where('type', 'monthly')->first();
        $this->assertSame('October 2026', $report->periodLabel());
        $this->assertSame('2026-10-31', $report->report_date->toDateString());
    }

    public function test_monthly_report_survives_new_session(): void
    {
        $manager = $this->manager();

        ManagerReport::create([
            'user_id' => $manager->id,
            'type' => 'monthly',
            'report_date' => '2026-09-30',
            'month' => 9,
            'year' => 2026,
            'revenue_overall' => 90000,
        ]);

        // Fresh session — record must still be retrievable
        $this->actingAs($manager);

        $this->get(route('manager.reports.index'))
            ->assertOk()
            ->assertSee('September 2026');
    }

    public function test_non_manager_cannot_access_manager_reports(): void
    {
        Role::findOrCreate('reception', 'web');
        $user = User::factory()->create();
        $user->assignRole('reception');

        $this->actingAs($user)
            ->get(route('manager.reports.index'))
            ->assertRedirect();
    }
}
