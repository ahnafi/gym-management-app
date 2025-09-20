<?php

namespace Tests\Feature\Api;

use Tests\ApiTestCase;
use App\Models\User;
use App\Models\PersonalTrainer;
use App\Models\PersonalTrainerPackage;
use App\Models\PersonalTrainerAssignment;
use App\Models\PersonalTrainerSchedule;

class PersonalTrainerControllerTest extends ApiTestCase
{
    /** @test */
    public function anyone_can_get_available_personal_trainers()
    {
        PersonalTrainer::factory()->count(3)->create(['is_available' => true]);
        PersonalTrainer::factory()->count(2)->create(['is_available' => false]);

        $response = $this->getJson('/api/personal-trainers');

        $this->assertApiResponse($response);
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'specialization',
                    'experience_years',
                    'hourly_rate',
                    'rating',
                    'user' => [
                        'id',
                        'name'
                    ]
                ]
            ]
        ]);
    }

    /** @test */
    public function admin_can_get_all_trainers_including_unavailable()
    {
        $admin = $this->actingAsAdmin();
        PersonalTrainer::factory()->count(3)->create(['is_available' => true]);
        PersonalTrainer::factory()->count(2)->create(['is_available' => false]);

        $response = $this->getJson('/api/personal-trainers?include_unavailable=true');

        $this->assertApiResponse($response);
        $response->assertJsonCount(5, 'data');
    }

    /** @test */
    public function trainer_can_create_their_profile()
    {
        $trainerUser = $this->actingAsTrainer();

        $profileData = [
            'specialization' => ['Weight Loss', 'Muscle Building'],
            'experience_years' => 5,
            'hourly_rate' => 200000,
            'bio' => 'Experienced fitness trainer',
            'certifications' => ['Certified Personal Trainer', 'Nutrition Specialist']
        ];

        $response = $this->postJson('/api/personal-trainers/profile', $profileData);

        $this->assertApiResponse($response, 201);
        $this->assertDatabaseHas('personal_trainers', [
            'user_id' => $trainerUser->id,
            'experience_years' => 5,
            'hourly_rate' => 200000
        ]);
    }

    /** @test */
    public function member_cannot_create_trainer_profile()
    {
        $member = $this->actingAsMember();

        $profileData = [
            'specialization' => ['Weight Loss'],
            'experience_years' => 3,
            'hourly_rate' => 150000,
            'bio' => 'Fitness trainer'
        ];

        $response = $this->postJson('/api/personal-trainers/profile', $profileData);

        $this->assertForbidden($response);
    }

    /** @test */
    public function trainer_can_update_their_profile()
    {
        $trainerUser = $this->actingAsTrainer();
        $trainer = PersonalTrainer::factory()->create(['user_id' => $trainerUser->id]);

        $updateData = [
            'hourly_rate' => 250000,
            'bio' => 'Updated bio',
            'is_available' => false
        ];

        $response = $this->putJson('/api/personal-trainers/profile', $updateData);

        $this->assertApiResponse($response);
        $this->assertDatabaseHas('personal_trainers', [
            'id' => $trainer->id,
            'hourly_rate' => 250000,
            'is_available' => false
        ]);
    }

    /** @test */
    public function admin_can_update_any_trainer_profile()
    {
        $admin = $this->actingAsAdmin();
        $trainer = PersonalTrainer::factory()->create();

        $updateData = [
            'is_available' => false,
            'rating' => 4.5
        ];

        $response = $this->putJson("/api/personal-trainers/{$trainer->id}", $updateData);

        $this->assertApiResponse($response);
        $this->assertDatabaseHas('personal_trainers', [
            'id' => $trainer->id,
            'is_available' => false,
            'rating' => 4.5
        ]);
    }

    /** @test */
    public function trainer_can_create_training_package()
    {
        $trainerUser = $this->actingAsTrainer();
        $trainer = PersonalTrainer::factory()->create(['user_id' => $trainerUser->id]);

        $packageData = [
            'name' => 'Basic Training Package',
            'description' => 'Basic personal training sessions',
            'price' => 1000000,
            'session_count' => 8,
            'duration_days' => 60,
            'includes_nutrition' => false
        ];

        $response = $this->postJson('/api/personal-trainers/packages', $packageData);

        $this->assertApiResponse($response, 201);
        $this->assertDatabaseHas('personal_trainer_packages', [
            'personal_trainer_id' => $trainer->id,
            'name' => 'Basic Training Package',
            'price' => 1000000,
            'session_count' => 8
        ]);
    }

    /** @test */
    public function trainer_can_get_their_packages()
    {
        $trainerUser = $this->actingAsTrainer();
        $trainer = PersonalTrainer::factory()->create(['user_id' => $trainerUser->id]);
        
        PersonalTrainerPackage::factory()->count(3)->create([
            'personal_trainer_id' => $trainer->id
        ]);

        $response = $this->getJson('/api/personal-trainers/my-packages');

        $this->assertApiResponse($response);
        $response->assertJsonCount(3, 'data');
    }

    /** @test */
    public function anyone_can_get_trainer_packages()
    {
        $trainer = PersonalTrainer::factory()->create();
        PersonalTrainerPackage::factory()->count(2)->create([
            'personal_trainer_id' => $trainer->id,
            'is_active' => true
        ]);

        $response = $this->getJson("/api/personal-trainers/{$trainer->id}/packages");

        $this->assertApiResponse($response);
        $response->assertJsonCount(2, 'data');
    }

    /** @test */
    public function authenticated_user_can_purchase_trainer_package()
    {
        $user = $this->actingAsMember();
        $package = PersonalTrainerPackage::factory()->create(['price' => 1000000]);
        
        $mock = $this->mockMidtrans();
        $mock->method('createTransaction')
             ->willReturn([
                 'transaction_id' => 'test_transaction_123',
                 'gross_amount' => 1000000,
                 'payment_type' => 'credit_card'
             ]);

        $response = $this->postJson("/api/personal-trainers/packages/{$package->id}/purchase");

        $this->assertApiResponse($response, 201);
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type' => 'personal_trainer',
            'item_id' => $package->id,
            'amount' => 1000000,
            'status' => 'pending'
        ]);
    }

    /** @test */
    public function admin_can_assign_trainer_to_member()
    {
        $admin = $this->actingAsAdmin();
        $trainer = PersonalTrainer::factory()->create();
        $member = User::factory()->create(['role' => 'member']);
        $package = PersonalTrainerPackage::factory()->create([
            'personal_trainer_id' => $trainer->id
        ]);

        $assignmentData = [
            'user_id' => $member->id,
            'personal_trainer_package_id' => $package->id,
            'session_count' => 8,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addDays(60)->format('Y-m-d')
        ];

        $response = $this->postJson('/api/personal-trainers/assignments', $assignmentData);

        $this->assertApiResponse($response, 201);
        $this->assertDatabaseHas('personal_trainer_assignments', [
            'user_id' => $member->id,
            'personal_trainer_id' => $trainer->id,
            'session_count' => 8
        ]);
    }

    /** @test */
    public function trainer_can_get_their_assignments()
    {
        $trainerUser = $this->actingAsTrainer();
        $trainer = PersonalTrainer::factory()->create(['user_id' => $trainerUser->id]);
        
        PersonalTrainerAssignment::factory()->count(3)->create([
            'personal_trainer_id' => $trainer->id
        ]);

        $response = $this->getJson('/api/personal-trainers/my-assignments');

        $this->assertApiResponse($response);
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'session_count',
                    'remaining_sessions',
                    'start_date',
                    'end_date',
                    'status',
                    'user' => [
                        'id',
                        'name',
                        'email'
                    ]
                ]
            ]
        ]);
    }

    /** @test */
    public function member_can_get_their_trainer_assignments()
    {
        $member = $this->actingAsMember();
        
        PersonalTrainerAssignment::factory()->count(2)->create([
            'user_id' => $member->id
        ]);

        $response = $this->getJson('/api/personal-trainers/my-trainer');

        $this->assertApiResponse($response);
        $response->assertJsonCount(2, 'data');
    }

    /** @test */
    public function trainer_can_create_training_schedule()
    {
        $trainerUser = $this->actingAsTrainer();
        $trainer = PersonalTrainer::factory()->create(['user_id' => $trainerUser->id]);
        $assignment = PersonalTrainerAssignment::factory()->create([
            'personal_trainer_id' => $trainer->id,
            'remaining_sessions' => 5
        ]);

        $scheduleData = [
            'assignment_id' => $assignment->id,
            'scheduled_date' => now()->addDays(3)->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '10:00',
            'notes' => 'Upper body workout'
        ];

        $response = $this->postJson('/api/personal-trainers/schedules', $scheduleData);

        $this->assertApiResponse($response, 201);
        $this->assertDatabaseHas('personal_trainer_schedules', [
            'personal_trainer_assignment_id' => $assignment->id,
            'scheduled_date' => now()->addDays(3)->format('Y-m-d'),
            'status' => 'scheduled'
        ]);
    }

    /** @test */
    public function trainer_can_complete_training_session()
    {
        $trainerUser = $this->actingAsTrainer();
        $trainer = PersonalTrainer::factory()->create(['user_id' => $trainerUser->id]);
        $assignment = PersonalTrainerAssignment::factory()->create([
            'personal_trainer_id' => $trainer->id,
            'remaining_sessions' => 5
        ]);
        
        $schedule = PersonalTrainerSchedule::factory()->create([
            'personal_trainer_assignment_id' => $assignment->id,
            'status' => 'scheduled'
        ]);

        $completionData = [
            'status' => 'completed',
            'training_notes' => 'Great session, client improved strength',
            'feedback' => 'Keep up the good work!'
        ];

        $response = $this->putJson("/api/personal-trainers/schedules/{$schedule->id}", $completionData);

        $this->assertApiResponse($response);
        $this->assertDatabaseHas('personal_trainer_schedules', [
            'id' => $schedule->id,
            'status' => 'completed',
            'training_notes' => 'Great session, client improved strength'
        ]);

        $assignment->refresh();
        $this->assertEquals(4, $assignment->remaining_sessions);
    }

    /** @test */
    public function member_can_get_their_training_schedules()
    {
        $member = $this->actingAsMember();
        $assignment = PersonalTrainerAssignment::factory()->create([
            'user_id' => $member->id
        ]);
        
        PersonalTrainerSchedule::factory()->count(3)->create([
            'personal_trainer_assignment_id' => $assignment->id
        ]);

        $response = $this->getJson('/api/personal-trainers/my-schedules');

        $this->assertApiResponse($response);
        $response->assertJsonCount(3, 'data');
    }

    /** @test */
    public function user_can_filter_trainers_by_specialization()
    {
        PersonalTrainer::factory()->create([
            'specialization' => ['Weight Loss', 'Cardio'],
            'is_available' => true
        ]);
        PersonalTrainer::factory()->create([
            'specialization' => ['Muscle Building', 'Strength'],
            'is_available' => true
        ]);

        $response = $this->getJson('/api/personal-trainers?specialization=Weight Loss');

        $this->assertApiResponse($response);
        $response->assertJsonCount(1, 'data');
    }

    /** @test */
    public function user_can_filter_trainers_by_experience()
    {
        PersonalTrainer::factory()->create([
            'experience_years' => 2,
            'is_available' => true
        ]);
        PersonalTrainer::factory()->create([
            'experience_years' => 8,
            'is_available' => true
        ]);

        $response = $this->getJson('/api/personal-trainers?min_experience=5');

        $this->assertApiResponse($response);
        $response->assertJsonCount(1, 'data');
    }

    /** @test */
    public function user_can_filter_trainers_by_hourly_rate()
    {
        PersonalTrainer::factory()->create([
            'hourly_rate' => 150000,
            'is_available' => true
        ]);
        PersonalTrainer::factory()->create([
            'hourly_rate' => 300000,
            'is_available' => true
        ]);

        $response = $this->getJson('/api/personal-trainers?max_rate=200000');

        $this->assertApiResponse($response);
        $response->assertJsonCount(1, 'data');
    }

    /** @test */
    public function admin_can_get_trainer_statistics()
    {
        $admin = $this->actingAsAdmin();
        
        PersonalTrainer::factory()->count(5)->create();
        PersonalTrainerPackage::factory()->count(8)->create();
        PersonalTrainerAssignment::factory()->count(10)->create();

        $response = $this->getJson('/api/personal-trainers/statistics');

        $this->assertApiResponse($response);
        $response->assertJsonStructure([
            'data' => [
                'total_trainers',
                'available_trainers',
                'total_packages',
                'active_assignments',
                'completed_sessions',
                'average_rating',
                'revenue_this_month'
            ]
        ]);
    }

    /** @test */
    public function trainer_profile_creation_requires_valid_data()
    {
        $trainerUser = $this->actingAsTrainer();

        $response = $this->postJson('/api/personal-trainers/profile', []);

        $this->assertValidationError($response, ['specialization', 'experience_years', 'hourly_rate']);
    }

    /** @test */
    public function package_creation_requires_valid_data()
    {
        $trainerUser = $this->actingAsTrainer();
        PersonalTrainer::factory()->create(['user_id' => $trainerUser->id]);

        $response = $this->postJson('/api/personal-trainers/packages', []);

        $this->assertValidationError($response, ['name', 'price', 'session_count', 'duration_days']);
    }

    /** @test */
    public function trainer_cannot_schedule_session_for_others_assignments()
    {
        $trainerUser = $this->actingAsTrainer();
        $trainer = PersonalTrainer::factory()->create(['user_id' => $trainerUser->id]);
        $otherTrainer = PersonalTrainer::factory()->create();
        
        $assignment = PersonalTrainerAssignment::factory()->create([
            'personal_trainer_id' => $otherTrainer->id
        ]);

        $scheduleData = [
            'assignment_id' => $assignment->id,
            'scheduled_date' => now()->addDays(3)->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '10:00'
        ];

        $response = $this->postJson('/api/personal-trainers/schedules', $scheduleData);

        $this->assertForbidden($response);
    }

    /** @test */
    public function trainer_cannot_schedule_session_with_no_remaining_sessions()
    {
        $trainerUser = $this->actingAsTrainer();
        $trainer = PersonalTrainer::factory()->create(['user_id' => $trainerUser->id]);
        $assignment = PersonalTrainerAssignment::factory()->create([
            'personal_trainer_id' => $trainer->id,
            'remaining_sessions' => 0
        ]);

        $scheduleData = [
            'assignment_id' => $assignment->id,
            'scheduled_date' => now()->addDays(3)->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '10:00'
        ];

        $response = $this->postJson('/api/personal-trainers/schedules', $scheduleData);

        $this->assertErrorResponse($response, 422);
        $response->assertJson([
            'success' => false,
            'message' => 'No remaining sessions for this assignment'
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_purchase_trainer_package()
    {
        $package = PersonalTrainerPackage::factory()->create();

        $response = $this->postJson("/api/personal-trainers/packages/{$package->id}/purchase");

        $this->assertUnauthorized($response);
    }
}