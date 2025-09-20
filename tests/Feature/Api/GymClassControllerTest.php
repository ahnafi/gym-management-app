<?php

namespace Tests\Feature\Api;

use Tests\ApiTestCase;
use App\Models\User;
use App\Models\GymClass;
use App\Models\GymClassSchedule;
use App\Models\GymClassAttendance;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class GymClassControllerTest extends ApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function anyone_can_get_active_gym_classes()
    {
        GymClass::factory()->count(3)->create(['is_active' => true]);
        GymClass::factory()->count(2)->create(['is_active' => false]);

        $response = $this->getJson('/api/gym-classes');

        $this->assertApiResponse($response);
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'instructor',
                    'max_participants',
                    'duration_minutes'
                ]
            ]
        ]);
    }

    /** @test */
    public function admin_can_create_gym_class()
    {
        $admin = $this->actingAsAdmin();

        $classData = [
            'name' => 'Yoga Class',
            'description' => 'Relaxing yoga session',
            'instructor' => 'Jane Doe',
            'max_participants' => 20,
            'duration_minutes' => 60,
            'requirements' => 'Bring your own mat'
        ];

        $response = $this->postJson('/api/gym-classes', $classData);

        $this->assertApiResponse($response, 201);
        $this->assertDatabaseHas('gym_classes', [
            'name' => 'Yoga Class',
            'instructor' => 'Jane Doe',
            'max_participants' => 20
        ]);
    }

    /** @test */
    public function admin_can_create_class_with_image()
    {
        $admin = $this->actingAsAdmin();
        $image = $this->createTestImage('class.jpg');

        $classData = [
            'name' => 'Yoga Class',
            'description' => 'Relaxing yoga session',
            'instructor' => 'Jane Doe',
            'max_participants' => 20,
            'duration_minutes' => 60,
            'image' => $image
        ];

        $response = $this->postJson('/api/gym-classes', $classData);

        $this->assertApiResponse($response, 201);
        Storage::assertExists('public/classes/' . $image->hashName());
    }

    /** @test */
    public function member_cannot_create_gym_class()
    {
        $member = $this->actingAsMember();

        $classData = [
            'name' => 'Test Class',
            'instructor' => 'John Doe',
            'max_participants' => 10,
            'duration_minutes' => 45
        ];

        $response = $this->postJson('/api/gym-classes', $classData);

        $this->assertForbidden($response);
    }

    /** @test */
    public function admin_can_update_gym_class()
    {
        $admin = $this->actingAsAdmin();
        $gymClass = GymClass::factory()->create();

        $updateData = [
            'name' => 'Updated Class Name',
            'max_participants' => 25,
            'is_active' => false
        ];

        $response = $this->putJson("/api/gym-classes/{$gymClass->id}", $updateData);

        $this->assertApiResponse($response);
        $this->assertDatabaseHas('gym_classes', [
            'id' => $gymClass->id,
            'name' => 'Updated Class Name',
            'max_participants' => 25,
            'is_active' => false
        ]);
    }

    /** @test */
    public function admin_can_delete_gym_class()
    {
        $admin = $this->actingAsAdmin();
        $gymClass = GymClass::factory()->create();

        $response = $this->deleteJson("/api/gym-classes/{$gymClass->id}");

        $this->assertApiResponse($response);
        $this->assertDatabaseMissing('gym_classes', ['id' => $gymClass->id]);
    }

    /** @test */
    public function anyone_can_get_class_schedules()
    {
        $gymClass = GymClass::factory()->create();
        GymClassSchedule::factory()->count(3)->create([
            'gym_class_id' => $gymClass->id,
            'status' => 'scheduled'
        ]);

        $response = $this->getJson("/api/gym-classes/{$gymClass->id}/schedules");

        $this->assertApiResponse($response);
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'start_time',
                    'end_time',
                    'available_slots',
                    'booked_slots',
                    'status'
                ]
            ]
        ]);
    }

    /** @test */
    public function admin_can_create_class_schedule()
    {
        $admin = $this->actingAsAdmin();
        $gymClass = GymClass::factory()->create(['max_participants' => 20]);

        $scheduleData = [
            'start_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'end_time' => now()->addDay()->addHour()->format('Y-m-d H:i:s'),
            'available_slots' => 15
        ];

        $response = $this->postJson("/api/gym-classes/{$gymClass->id}/schedules", $scheduleData);

        $this->assertApiResponse($response, 201);
        $this->assertDatabaseHas('gym_class_schedules', [
            'gym_class_id' => $gymClass->id,
            'available_slots' => 15,
            'status' => 'scheduled'
        ]);
    }

    /** @test */
    public function schedule_cannot_exceed_class_max_participants()
    {
        $admin = $this->actingAsAdmin();
        $gymClass = GymClass::factory()->create(['max_participants' => 20]);

        $scheduleData = [
            'start_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'end_time' => now()->addDay()->addHour()->format('Y-m-d H:i:s'),
            'available_slots' => 25
        ];

        $response = $this->postJson("/api/gym-classes/{$gymClass->id}/schedules", $scheduleData);

        $this->assertValidationError($response, ['available_slots']);
    }

    /** @test */
    public function authenticated_user_can_book_class()
    {
        $user = $this->actingAsMember([
            'membership_status' => 'active',
            'membership_end_date' => now()->addMonth()
        ]);
        
        $schedule = GymClassSchedule::factory()->upcoming()->create([
            'available_slots' => 10,
            'booked_slots' => 5
        ]);

        $response = $this->postJson("/api/gym-classes/schedules/{$schedule->id}/book");

        $this->assertApiResponse($response, 201);
        $this->assertDatabaseHas('gym_class_attendances', [
            'user_id' => $user->id,
            'gym_class_schedule_id' => $schedule->id,
            'status' => 'booked'
        ]);

        $schedule->refresh();
        $this->assertEquals(6, $schedule->booked_slots);
    }

    /** @test */
    public function user_cannot_book_class_without_active_membership()
    {
        $user = $this->actingAsMember([
            'membership_status' => 'inactive'
        ]);
        
        $schedule = GymClassSchedule::factory()->upcoming()->create();

        $response = $this->postJson("/api/gym-classes/schedules/{$schedule->id}/book");

        $this->assertErrorResponse($response, 422);
        $response->assertJson([
            'success' => false,
            'message' => 'Active membership required to book classes'
        ]);
    }

    /** @test */
    public function user_cannot_book_full_class()
    {
        $user = $this->actingAsMember([
            'membership_status' => 'active',
            'membership_end_date' => now()->addMonth()
        ]);
        
        $schedule = GymClassSchedule::factory()->full()->create();

        $response = $this->postJson("/api/gym-classes/schedules/{$schedule->id}/book");

        $this->assertErrorResponse($response, 422);
        $response->assertJson([
            'success' => false,
            'message' => 'Class is already full'
        ]);
    }

    /** @test */
    public function user_cannot_book_same_class_twice()
    {
        $user = $this->actingAsMember([
            'membership_status' => 'active',
            'membership_end_date' => now()->addMonth()
        ]);
        
        $schedule = GymClassSchedule::factory()->upcoming()->create();
        
        // First booking
        GymClassAttendance::factory()->create([
            'user_id' => $user->id,
            'gym_class_schedule_id' => $schedule->id,
            'status' => 'booked'
        ]);

        $response = $this->postJson("/api/gym-classes/schedules/{$schedule->id}/book");

        $this->assertErrorResponse($response, 422);
        $response->assertJson([
            'success' => false,
            'message' => 'You have already booked this class'
        ]);
    }

    /** @test */
    public function user_can_cancel_booking_before_24_hours()
    {
        $user = $this->actingAsMember();
        
        $schedule = GymClassSchedule::factory()->create([
            'start_time' => now()->addDays(2),
            'booked_slots' => 5
        ]);
        
        $attendance = GymClassAttendance::factory()->create([
            'user_id' => $user->id,
            'gym_class_schedule_id' => $schedule->id,
            'status' => 'booked'
        ]);

        $response = $this->putJson("/api/gym-classes/bookings/{$attendance->id}/cancel");

        $this->assertApiResponse($response);
        $this->assertDatabaseHas('gym_class_attendances', [
            'id' => $attendance->id,
            'status' => 'cancelled'
        ]);

        $schedule->refresh();
        $this->assertEquals(4, $schedule->booked_slots);
    }

    /** @test */
    public function user_cannot_cancel_booking_within_24_hours()
    {
        $user = $this->actingAsMember();
        
        $schedule = GymClassSchedule::factory()->create([
            'start_time' => now()->addHours(12)
        ]);
        
        $attendance = GymClassAttendance::factory()->create([
            'user_id' => $user->id,
            'gym_class_schedule_id' => $schedule->id,
            'status' => 'booked'
        ]);

        $response = $this->putJson("/api/gym-classes/bookings/{$attendance->id}/cancel");

        $this->assertErrorResponse($response, 422);
        $response->assertJson([
            'success' => false,
            'message' => 'Cannot cancel booking less than 24 hours before class'
        ]);
    }

    /** @test */
    public function user_can_get_their_bookings()
    {
        $user = $this->actingAsMember();
        
        GymClassAttendance::factory()->count(3)->create([
            'user_id' => $user->id
        ]);
        
        // Create attendance for other user (should not appear)
        $otherUser = User::factory()->create();
        GymClassAttendance::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getJson('/api/gym-classes/my-bookings');

        $this->assertPaginatedResponse($response);
        $response->assertJsonCount(3, 'data.data');
        $response->assertJsonStructure([
            'data' => [
                'data' => [
                    '*' => [
                        'id',
                        'status',
                        'booking_date',
                        'schedule' => [
                            'start_time',
                            'end_time',
                            'gym_class'
                        ]
                    ]
                ]
            ]
        ]);
    }

    /** @test */
    public function admin_can_mark_attendance()
    {
        $admin = $this->actingAsAdmin();
        
        $attendance = GymClassAttendance::factory()->create(['status' => 'booked']);

        $response = $this->putJson("/api/gym-classes/attendances/{$attendance->id}/mark", [
            'status' => 'attended'
        ]);

        $this->assertApiResponse($response);
        $this->assertDatabaseHas('gym_class_attendances', [
            'id' => $attendance->id,
            'status' => 'attended'
        ]);
    }

    /** @test */
    public function member_cannot_mark_attendance()
    {
        $member = $this->actingAsMember();
        $attendance = GymClassAttendance::factory()->create();

        $response = $this->putJson("/api/gym-classes/attendances/{$attendance->id}/mark", [
            'status' => 'attended'
        ]);

        $this->assertForbidden($response);
    }

    /** @test */
    public function admin_can_get_class_statistics()
    {
        $admin = $this->actingAsAdmin();
        
        GymClass::factory()->count(3)->create();
        GymClassSchedule::factory()->count(5)->create();
        GymClassAttendance::factory()->count(10)->create(['status' => 'attended']);

        $response = $this->getJson('/api/gym-classes/statistics');

        $this->assertApiResponse($response);
        $response->assertJsonStructure([
            'data' => [
                'total_classes',
                'active_classes',
                'total_schedules',
                'total_bookings',
                'attendance_rate',
                'popular_classes'
            ]
        ]);
    }

    /** @test */
    public function class_creation_requires_valid_data()
    {
        $admin = $this->actingAsAdmin();

        $response = $this->postJson('/api/gym-classes', []);

        $this->assertValidationError($response, ['name', 'instructor', 'max_participants', 'duration_minutes']);
    }

    /** @test */
    public function schedule_start_time_must_be_in_future()
    {
        $admin = $this->actingAsAdmin();
        $gymClass = GymClass::factory()->create();

        $scheduleData = [
            'start_time' => now()->subHour()->format('Y-m-d H:i:s'),
            'end_time' => now()->format('Y-m-d H:i:s'),
            'available_slots' => 15
        ];

        $response = $this->postJson("/api/gym-classes/{$gymClass->id}/schedules", $scheduleData);

        $this->assertValidationError($response, ['start_time']);
    }

    /** @test */
    public function schedule_end_time_must_be_after_start_time()
    {
        $admin = $this->actingAsAdmin();
        $gymClass = GymClass::factory()->create();

        $scheduleData = [
            'start_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'end_time' => now()->addDay()->subHour()->format('Y-m-d H:i:s'),
            'available_slots' => 15
        ];

        $response = $this->postJson("/api/gym-classes/{$gymClass->id}/schedules", $scheduleData);

        $this->assertValidationError($response, ['end_time']);
    }

    /** @test */
    public function user_can_filter_classes_by_instructor()
    {
        GymClass::factory()->create(['instructor' => 'John Doe']);
        GymClass::factory()->create(['instructor' => 'Jane Smith']);

        $response = $this->getJson('/api/gym-classes?instructor=John Doe');

        $this->assertApiResponse($response);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['instructor' => 'John Doe']);
    }

    /** @test */
    public function user_can_filter_schedules_by_date()
    {
        $gymClass = GymClass::factory()->create();
        $tomorrow = Carbon::tomorrow();
        
        GymClassSchedule::factory()->create([
            'gym_class_id' => $gymClass->id,
            'start_time' => $tomorrow->copy()->setTime(9, 0)
        ]);
        
        GymClassSchedule::factory()->create([
            'gym_class_id' => $gymClass->id,
            'start_time' => $tomorrow->copy()->addDay()->setTime(9, 0)
        ]);

        $response = $this->getJson("/api/gym-classes/{$gymClass->id}/schedules?date=" . $tomorrow->format('Y-m-d'));

        $this->assertApiResponse($response);
        $response->assertJsonCount(1, 'data');
    }

    /** @test */
    public function unauthenticated_user_cannot_book_classes()
    {
        $schedule = GymClassSchedule::factory()->upcoming()->create();

        $response = $this->postJson("/api/gym-classes/schedules/{$schedule->id}/book");

        $this->assertUnauthorized($response);
    }

    /** @test */
    public function admin_can_get_schedule_with_attendees()
    {
        $admin = $this->actingAsAdmin();
        $schedule = GymClassSchedule::factory()->create();
        
        GymClassAttendance::factory()->count(3)->create([
            'gym_class_schedule_id' => $schedule->id
        ]);

        $response = $this->getJson("/api/gym-classes/schedules/{$schedule->id}/attendees");

        $this->assertApiResponse($response);
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'status',
                    'booking_date',
                    'user' => [
                        'id',
                        'name',
                        'email'
                    ]
                ]
            ]
        ]);
    }
}