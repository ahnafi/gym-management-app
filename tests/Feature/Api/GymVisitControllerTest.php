<?php

namespace Tests\Feature\Api;

use Tests\ApiTestCase;
use App\Models\User;
use App\Models\GymVisit;
use Carbon\Carbon;

class GymVisitControllerTest extends ApiTestCase
{
    /** @test */
    public function authenticated_user_can_check_in()
    {
        $user = $this->actingAsMember([
            'membership_status' => 'active',
            'membership_end_date' => now()->addMonth()
        ]);

        $response = $this->postJson('/api/gym-visits/check-in');

        $this->assertApiResponse($response, 201);
        $this->assertDatabaseHas('gym_visits', [
            'user_id' => $user->id,
            'visit_date' => now()->format('Y-m-d'),
            'check_out_time' => null
        ]);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'check_in_time',
                'visit_date'
            ]
        ]);
    }

    /** @test */
    public function user_cannot_check_in_without_active_membership()
    {
        $user = $this->actingAsMember([
            'membership_status' => 'inactive'
        ]);

        $response = $this->postJson('/api/gym-visits/check-in');

        $this->assertErrorResponse($response, 422);
        $response->assertJson([
            'success' => false,
            'message' => 'Active membership required to access gym'
        ]);
    }

    /** @test */
    public function user_cannot_check_in_twice_same_day_without_checking_out()
    {
        $user = $this->actingAsMember([
            'membership_status' => 'active',
            'membership_end_date' => now()->addMonth()
        ]);

        // First check-in
        GymVisit::factory()->checkedIn()->create([
            'user_id' => $user->id,
            'visit_date' => now()->format('Y-m-d')
        ]);

        $response = $this->postJson('/api/gym-visits/check-in');

        $this->assertErrorResponse($response, 422);
        $response->assertJson([
            'success' => false,
            'message' => 'You are already checked in'
        ]);
    }

    /** @test */
    public function authenticated_user_can_check_out()
    {
        $user = $this->actingAsMember();
        
        $visit = GymVisit::factory()->checkedIn()->create([
            'user_id' => $user->id,
            'check_in_time' => now()->subHours(2),
            'visit_date' => now()->format('Y-m-d')
        ]);

        $response = $this->postJson('/api/gym-visits/check-out');

        $this->assertApiResponse($response);
        
        $visit->refresh();
        $this->assertNotNull($visit->check_out_time);
        $this->assertNotNull($visit->duration_minutes);
    }

    /** @test */
    public function user_cannot_check_out_without_checking_in()
    {
        $user = $this->actingAsMember();

        $response = $this->postJson('/api/gym-visits/check-out');

        $this->assertErrorResponse($response, 422);
        $response->assertJson([
            'success' => false,
            'message' => 'You are not checked in'
        ]);
    }

    /** @test */
    public function authenticated_user_can_get_their_visit_history()
    {
        $user = $this->actingAsMember();
        
        GymVisit::factory()->count(5)->create(['user_id' => $user->id]);
        
        // Create visits for other user (should not appear)
        $otherUser = User::factory()->create();
        GymVisit::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getJson('/api/gym-visits/my-visits');

        $this->assertPaginatedResponse($response);
        $response->assertJsonCount(5, 'data.data');
        $response->assertJsonStructure([
            'data' => [
                'data' => [
                    '*' => [
                        'id',
                        'check_in_time',
                        'check_out_time',
                        'visit_date',
                        'duration_minutes'
                    ]
                ]
            ]
        ]);
    }

    /** @test */
    public function user_can_filter_visit_history_by_date_range()
    {
        $user = $this->actingAsMember();
        
        // Create visits for different dates
        GymVisit::factory()->create([
            'user_id' => $user->id,
            'visit_date' => '2024-01-15'
        ]);
        GymVisit::factory()->create([
            'user_id' => $user->id,
            'visit_date' => '2024-01-20'
        ]);
        GymVisit::factory()->create([
            'user_id' => $user->id,
            'visit_date' => '2024-01-25'
        ]);

        $response = $this->getJson('/api/gym-visits/my-visits?start_date=2024-01-18&end_date=2024-01-22');

        $this->assertPaginatedResponse($response);
        $response->assertJsonCount(1, 'data.data');
        $response->assertJsonFragment(['visit_date' => '2024-01-20']);
    }

    /** @test */
    public function authenticated_user_can_get_visit_statistics()
    {
        $user = $this->actingAsMember();
        
        // Create visits for current month
        GymVisit::factory()->count(3)->create([
            'user_id' => $user->id,
            'visit_date' => now()->format('Y-m-d')
        ]);
        
        // Create visits for this week
        GymVisit::factory()->count(2)->create([
            'user_id' => $user->id,
            'visit_date' => now()->startOfWeek()->format('Y-m-d')
        ]);

        $response = $this->getJson('/api/gym-visits/my-statistics');

        $this->assertApiResponse($response);
        $response->assertJsonStructure([
            'data' => [
                'total_visits',
                'visits_this_month',
                'visits_this_week',
                'average_duration',
                'current_streak',
                'longest_streak',
                'favorite_day',
                'monthly_trend'
            ]
        ]);
    }

    /** @test */
    public function admin_can_get_all_visits()
    {
        $admin = $this->actingAsAdmin();
        GymVisit::factory()->count(10)->create();

        $response = $this->getJson('/api/gym-visits');

        $this->assertPaginatedResponse($response);
        $response->assertJsonCount(10, 'data.data');
        $response->assertJsonStructure([
            'data' => [
                'data' => [
                    '*' => [
                        'id',
                        'check_in_time',
                        'check_out_time',
                        'visit_date',
                        'duration_minutes',
                        'user' => [
                            'id',
                            'name',
                            'email'
                        ]
                    ]
                ]
            ]
        ]);
    }

    /** @test */
    public function member_cannot_get_all_visits()
    {
        $member = $this->actingAsMember();

        $response = $this->getJson('/api/gym-visits');

        $this->assertForbidden($response);
    }

    /** @test */
    public function admin_can_filter_visits_by_user()
    {
        $admin = $this->actingAsAdmin();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        GymVisit::factory()->count(3)->create(['user_id' => $user1->id]);
        GymVisit::factory()->count(2)->create(['user_id' => $user2->id]);

        $response = $this->getJson("/api/gym-visits?user_id={$user1->id}");

        $this->assertPaginatedResponse($response);
        $response->assertJsonCount(3, 'data.data');
    }

    /** @test */
    public function admin_can_filter_visits_by_date()
    {
        $admin = $this->actingAsAdmin();
        
        GymVisit::factory()->create(['visit_date' => '2024-01-15']);
        GymVisit::factory()->create(['visit_date' => '2024-01-20']);

        $response = $this->getJson('/api/gym-visits?visit_date=2024-01-15');

        $this->assertPaginatedResponse($response);
        $response->assertJsonCount(1, 'data.data');
        $response->assertJsonFragment(['visit_date' => '2024-01-15']);
    }

    /** @test */
    public function admin_can_manually_create_visit_entry()
    {
        $admin = $this->actingAsAdmin();
        $user = User::factory()->create();

        $visitData = [
            'user_id' => $user->id,
            'visit_date' => now()->format('Y-m-d'),
            'check_in_time' => '09:00',
            'check_out_time' => '11:00',
            'duration_minutes' => 120
        ];

        $response = $this->postJson('/api/gym-visits/manual-entry', $visitData);

        $this->assertApiResponse($response, 201);
        $this->assertDatabaseHas('gym_visits', [
            'user_id' => $user->id,
            'visit_date' => now()->format('Y-m-d'),
            'duration_minutes' => 120
        ]);
    }

    /** @test */
    public function member_cannot_create_manual_visit_entry()
    {
        $member = $this->actingAsMember();
        $user = User::factory()->create();

        $visitData = [
            'user_id' => $user->id,
            'visit_date' => now()->format('Y-m-d'),
            'check_in_time' => '09:00',
            'check_out_time' => '11:00'
        ];

        $response = $this->postJson('/api/gym-visits/manual-entry', $visitData);

        $this->assertForbidden($response);
    }

    /** @test */
    public function admin_can_get_gym_visit_statistics()
    {
        $admin = $this->actingAsAdmin();
        
        GymVisit::factory()->count(20)->create();
        GymVisit::factory()->count(5)->create([
            'visit_date' => now()->format('Y-m-d')
        ]);

        $response = $this->getJson('/api/gym-visits/statistics');

        $this->assertApiResponse($response);
        $response->assertJsonStructure([
            'data' => [
                'total_visits',
                'visits_today',
                'visits_this_week',
                'visits_this_month',
                'active_members',
                'average_duration',
                'peak_hours',
                'daily_trend',
                'monthly_trend'
            ]
        ]);
    }

    /** @test */
    public function user_can_get_current_gym_status()
    {
        $user = $this->actingAsMember();
        
        GymVisit::factory()->checkedIn()->create([
            'user_id' => $user->id,
            'visit_date' => now()->format('Y-m-d'),
            'check_in_time' => now()->subHour()
        ]);

        $response = $this->getJson('/api/gym-visits/current-status');

        $this->assertApiResponse($response);
        $response->assertJsonFragment([
            'is_checked_in' => true
        ]);
        $response->assertJsonStructure([
            'data' => [
                'is_checked_in',
                'current_visit'
            ]
        ]);
    }

    /** @test */
    public function user_gets_correct_status_when_not_checked_in()
    {
        $user = $this->actingAsMember();

        $response = $this->getJson('/api/gym-visits/current-status');

        $this->assertApiResponse($response);
        $response->assertJsonFragment([
            'is_checked_in' => false,
            'current_visit' => null
        ]);
    }

    /** @test */
    public function admin_can_get_live_gym_occupancy()
    {
        $admin = $this->actingAsAdmin();
        
        // Create checked-in users
        GymVisit::factory()->count(5)->checkedIn()->create([
            'visit_date' => now()->format('Y-m-d')
        ]);
        
        // Create checked-out users (should not count)
        GymVisit::factory()->count(3)->completed()->create([
            'visit_date' => now()->format('Y-m-d')
        ]);

        $response = $this->getJson('/api/gym-visits/live-occupancy');

        $this->assertApiResponse($response);
        $response->assertJsonFragment([
            'current_occupancy' => 5
        ]);
        $response->assertJsonStructure([
            'data' => [
                'current_occupancy',
                'checked_in_users'
            ]
        ]);
    }

    /** @test */
    public function visit_statistics_calculate_streak_correctly()
    {
        $user = $this->actingAsMember();
        
        // Create consecutive visits for streak calculation
        for ($i = 1; $i <= 5; $i++) {
            GymVisit::factory()->create([
                'user_id' => $user->id,
                'visit_date' => now()->subDays($i)->format('Y-m-d')
            ]);
        }
        
        // Create today's visit
        GymVisit::factory()->create([
            'user_id' => $user->id,
            'visit_date' => now()->format('Y-m-d')
        ]);

        $response = $this->getJson('/api/gym-visits/my-statistics');

        $this->assertApiResponse($response);
        // Should have a streak of at least 5 days
        $response->assertJsonPath('data.current_streak', function ($streak) {
            return $streak >= 5;
        });
    }

    /** @test */
    public function manual_entry_requires_valid_data()
    {
        $admin = $this->actingAsAdmin();

        $response = $this->postJson('/api/gym-visits/manual-entry', []);

        $this->assertValidationError($response, ['user_id', 'visit_date', 'check_in_time']);
    }

    /** @test */
    public function manual_entry_validates_time_format()
    {
        $admin = $this->actingAsAdmin();
        $user = User::factory()->create();

        $visitData = [
            'user_id' => $user->id,
            'visit_date' => now()->format('Y-m-d'),
            'check_in_time' => 'invalid_time',
            'check_out_time' => 'also_invalid'
        ];

        $response = $this->postJson('/api/gym-visits/manual-entry', $visitData);

        $this->assertValidationError($response, ['check_in_time', 'check_out_time']);
    }

    /** @test */
    public function check_out_time_must_be_after_check_in_time()
    {
        $admin = $this->actingAsAdmin();
        $user = User::factory()->create();

        $visitData = [
            'user_id' => $user->id,
            'visit_date' => now()->format('Y-m-d'),
            'check_in_time' => '11:00',
            'check_out_time' => '09:00'
        ];

        $response = $this->postJson('/api/gym-visits/manual-entry', $visitData);

        $this->assertValidationError($response, ['check_out_time']);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_visit_endpoints()
    {
        $this->postJson('/api/gym-visits/check-in')->assertStatus(401);
        $this->postJson('/api/gym-visits/check-out')->assertStatus(401);
        $this->getJson('/api/gym-visits/my-visits')->assertStatus(401);
        $this->getJson('/api/gym-visits/my-statistics')->assertStatus(401);
        $this->getJson('/api/gym-visits/current-status')->assertStatus(401);
    }

    /** @test */
    public function visit_duration_is_calculated_correctly_on_checkout()
    {
        $user = $this->actingAsMember();
        
        $checkInTime = now()->subHours(2)->subMinutes(30);
        $visit = GymVisit::factory()->create([
            'user_id' => $user->id,
            'check_in_time' => $checkInTime,
            'visit_date' => now()->format('Y-m-d'),
            'check_out_time' => null,
            'duration_minutes' => null
        ]);

        $response = $this->postJson('/api/gym-visits/check-out');

        $this->assertApiResponse($response);
        
        $visit->refresh();
        $this->assertGreaterThan(140, $visit->duration_minutes); // Should be around 150 minutes
        $this->assertLessThan(160, $visit->duration_minutes);
    }
}