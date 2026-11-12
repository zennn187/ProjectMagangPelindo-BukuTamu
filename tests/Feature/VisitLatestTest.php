<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitLatestTest extends TestCase
{
    use RefreshDatabase;

    public function test_latest_visits_endpoint_returns_recent_visits(): void
    {
        $user = User::factory()->create([
            'role' => 'receptionist',
        ]);

        $employee = Employee::factory()->create();

        Visit::create([
            'visitor_name' => 'Budi Santoso',
            'visitor_phone' => '081234567890',
            'visitor_institution' => 'PT Contoh',
            'employee_id' => $employee->id,
            'purpose' => 'Meeting',
            'photo_path' => null,
            'qr_code_token' => 'token-123',
            'status' => Visit::STATUS_PENDING,
            'visit_type' => Visit::TYPE_MEET,
            'delivery_pref' => Visit::DELIVERY_HAND_IN,
        ]);

        $response = $this->actingAs($user)->getJson('/dashboard/latest');

        $response->assertOk();
        $response->assertJsonPath('visits.0.visitor_name', 'Budi Santoso');
        $response->assertJsonPath('visits.0.status', Visit::STATUS_PENDING);
        $response->assertJsonPath('counts.pending', 1);
    }

    public function test_latest_visits_endpoint_includes_database_photo_url(): void
    {
        $user = User::factory()->create([
            'role' => 'receptionist',
        ]);
        $visit = Visit::create([
            'visitor_name' => 'Budi Santoso',
            'visitor_phone' => '081234567890',
            'visitor_institution' => 'PT Contoh',
            'purpose' => 'Meeting',
            'qr_code_token' => 'token-with-photo',
            'status' => Visit::STATUS_PENDING,
            'visit_type' => Visit::TYPE_MEET,
        ]);
        $visit->photo()->create(['contents' => 'photo-binary']);

        $response = $this->actingAs($user)->getJson('/dashboard/latest');

        $response->assertOk()
            ->assertJsonPath('visits.0.photo_url', route('visits.photo', $visit, false));
    }

    public function test_photo_endpoint_streams_the_private_database_photo_to_authenticated_users(): void
    {
        $user = User::factory()->create([
            'role' => 'receptionist',
            'email_verified_at' => now(),
        ]);
        $visit = Visit::create([
            'visitor_name' => 'Budi Santoso',
            'visitor_phone' => '081234567890',
            'visitor_institution' => 'PT Contoh',
            'purpose' => 'Meeting',
            'qr_code_token' => 'token-photo-endpoint',
            'status' => Visit::STATUS_PENDING,
            'visit_type' => Visit::TYPE_MEET,
        ]);
        $visit->photo()->create(['contents' => 'photo-binary']);

        $response = $this->actingAs($user)->get(route('visits.photo', $visit));

        $response->assertOk()
            ->assertHeader('Content-Type', 'image/jpeg')
            ->assertSee('photo-binary', false);
    }

    public function test_dashboard_date_input_uses_valid_date_format(): void
    {
        $user = User::factory()->create([
            'role' => 'receptionist',
        ]);

        $response = $this->actingAs($user)->get('/dashboard?day=2026-08-30+00%3A00%3A00');

        $response->assertOk();
        $response->assertSee('value="2026-08-30"', false);
        $response->assertDontSee('value="2026-08-30 00:00:00"', false);
    }

    public function test_checkout_redirect_contains_the_correct_visitor_for_toast(): void
    {
        $user = User::factory()->create([
            'role' => 'receptionist',
        ]);

        $employee = Employee::factory()->create();
        $visit = Visit::create([
            'visitor_name' => 'Oza Okta',
            'visitor_phone' => '081234567890',
            'visitor_institution' => 'PT Contoh',
            'employee_id' => $employee->id,
            'purpose' => 'Meeting',
            'photo_path' => null,
            'qr_code_token' => 'token-checkout',
            'status' => Visit::STATUS_ACTIVE,
            'visit_type' => Visit::TYPE_MEET,
            'delivery_pref' => Visit::DELIVERY_HAND_IN,
        ]);

        $response = $this->actingAs($user)->post(route('visits.check-out', $visit));

        $response->assertRedirect();
        $response->assertSessionHas('visit_action.type', 'check-out');
        $response->assertSessionHas('visit_action.visit_id', $visit->id);
        $response->assertSessionHas('visit_action.visitor_name', 'Oza Okta');
        $this->assertSame(Visit::STATUS_COMPLETED, $visit->fresh()->status);
    }

    public function test_dashboard_status_actions_return_json_without_a_page_redirect(): void
    {
        $user = User::factory()->create([
            'role' => 'receptionist',
        ]);
        $visit = Visit::create([
            'visitor_name' => 'Budi Santoso',
            'visitor_phone' => '081234567890',
            'visitor_institution' => 'PT Contoh',
            'purpose' => 'Meeting',
            'qr_code_token' => 'token-json-action',
            'status' => Visit::STATUS_PENDING,
            'visit_type' => Visit::TYPE_MEET,
        ]);

        $response = $this->actingAs($user)->postJson(route('visits.check-in', $visit));

        $response->assertOk()
            ->assertJsonPath('visit_id', $visit->id)
            ->assertJsonPath('status', Visit::STATUS_ACTIVE);
        $this->assertSame(Visit::STATUS_ACTIVE, $visit->fresh()->status);

        $this->actingAs($user)->getJson('/dashboard/latest')
            ->assertJsonPath('counts.pending', 0)
            ->assertJsonPath('counts.active', 1);
    }
}
