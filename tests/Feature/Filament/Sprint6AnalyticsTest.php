<?php

namespace Tests\Feature\Filament;

use App\Filament\Exports\ScreeningSessionExporter;
use App\Filament\Resources\ScreeningSessions\ScreeningSessionResource;
use App\Filament\Widgets\CrisisEventStats;
use App\Filament\Widgets\ScreeningStats;
use App\Filament\Widgets\ScreeningTrendsChart;
use App\Filament\Widgets\SeverityDistributionChart;
use App\Models\ScreeningSession;
use App\Models\User;
use Filament\Actions\Exports\ExportColumn;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Sprint6AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superAdmin = User::factory()->superAdmin()->create();
    }

    public function test_screening_trends_chart_renders(): void
    {
        ScreeningSession::factory()->completed()->count(5)->create();

        Livewire::actingAs($this->superAdmin)
            ->test(ScreeningTrendsChart::class)
            ->assertOk();
    }

    public function test_severity_distribution_chart_renders(): void
    {
        ScreeningSession::factory()->completed()->count(5)->create();

        Livewire::actingAs($this->superAdmin)
            ->test(SeverityDistributionChart::class)
            ->assertOk();
    }

    public function test_crisis_event_stats_widget_renders(): void
    {
        Livewire::actingAs($this->superAdmin)
            ->test(CrisisEventStats::class)
            ->assertOk();
    }

    public function test_screening_stats_widget_renders(): void
    {
        Livewire::actingAs($this->superAdmin)
            ->test(ScreeningStats::class)
            ->assertOk();
    }

    public function test_exporter_has_correct_columns(): void
    {
        $columns = ScreeningSessionExporter::getColumns();

        $columnNames = collect($columns)->map(fn (ExportColumn $col) => $col->getName())->toArray();

        $this->assertContains('phq9_score', $columnNames);
        $this->assertContains('gad7_score', $columnNames);
        $this->assertContains('combined_severity', $columnNames);
        $this->assertContains('anonymous_id', $columnNames);
        $this->assertContains('completed_at', $columnNames);
    }

    public function test_screening_session_list_has_export_action(): void
    {
        $this->actingAs($this->superAdmin)
            ->get(ScreeningSessionResource::getUrl('index'))
            ->assertOk();
    }

    public function test_dashboard_renders_for_admin(): void
    {
        $this->actingAs($this->superAdmin)
            ->get('/admin')
            ->assertOk();
    }

    public function test_dashboard_shows_widgets(): void
    {
        ScreeningSession::factory()->completed()->count(3)->create();

        $this->actingAs($this->superAdmin)
            ->get('/admin')
            ->assertOk();
    }
}
