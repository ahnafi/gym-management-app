<?php

namespace Tests\Feature\Api;

use Tests\ApiTestCase;
use App\Models\User;

class UserControllerTest extends ApiTestCase
{
    /** @test */
    public function admin_can_get_all_users()
    {
        $admin = $this->actingAsAdmin();
        User::factory()->count(5)->create();

        $response = $this->getJson('/api/users');

        $this->assertPaginatedResponse($response);
        $response->assertJsonStructure([
            'data' => [
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'role',
                        'membership_status',
                        'created_at'
                    ]
                ]
            ]
        ]);
    }

    /** @test */
    public function member_cannot_get_all_users()
    {
        $member = $this->actingAsMember();

        $response = $this->getJson('/api/users');

        $this->assertForbidden($response);
    }

    /** @test */
    public function admin_can_search_users_by_name()
    {
        $admin = $this->actingAsAdmin();
        User::factory()->create(['name' => 'John Doe']);
        User::factory()->create(['name' => 'Jane Smith']);

        $response = $this->getJson('/api/users?search=John');

        $this->assertPaginatedResponse($response);
        $response->assertJsonFragment(['name' => 'John Doe']);
        $response->assertJsonMissing(['name' => 'Jane Smith']);
    }

    /** @test */
    public function admin_can_filter_users_by_role()
    {
        $admin = $this->actingAsAdmin();
        User::factory()->create(['role' => 'member']);
        User::factory()->create(['role' => 'trainer']);

        $response = $this->getJson('/api/users?role=member');

        $this->assertPaginatedResponse($response);
        $response->assertJsonFragment(['role' => 'member']);
    }

    /** @test */
    public function admin_can_filter_users_by_membership_status()
    {
        $admin = $this->actingAsAdmin();
        User::factory()->create(['membership_status' => 'active']);
        User::factory()->create(['membership_status' => 'inactive']);

        $response = $this->getJson('/api/users?membership_status=active');

        $this->assertPaginatedResponse($response);
        $response->assertJsonFragment(['membership_status' => 'active']);
    }

    /** @test */
    public function admin_can_view_specific_user()
    {
        $admin = $this->actingAsAdmin();
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);

        $response = $this->getJson("/api/users/{$user->id}");

        $this->assertApiResponse($response);
        $response->assertJsonFragment([
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);
    }

    /** @test */
    public function admin_cannot_view_nonexistent_user()
    {
        $admin = $this->actingAsAdmin();

        $response = $this->getJson('/api/users/99999');

        $this->assertNotFound($response);
    }

    /** @test */
    public function admin_can_create_user()
    {
        $admin = $this->actingAsAdmin();

        $userData = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '08123456789',
            'role' => 'member'
        ];

        $response = $this->postJson('/api/users', $userData);

        $this->assertApiResponse($response, 201);
        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'role' => 'member'
        ]);
    }

    /** @test */
    public function admin_cannot_create_user_with_invalid_data()
    {
        $admin = $this->actingAsAdmin();

        $response = $this->postJson('/api/users', []);

        $this->assertValidationError($response, ['name', 'email', 'password']);
    }

    /** @test */
    public function admin_cannot_create_user_with_duplicate_email()
    {
        $admin = $this->actingAsAdmin();
        $existingUser = User::factory()->create(['email' => 'existing@example.com']);

        $userData = [
            'name' => 'New User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '08123456789',
            'role' => 'member'
        ];

        $response = $this->postJson('/api/users', $userData);

        $this->assertValidationError($response, ['email']);
    }

    /** @test */
    public function admin_can_update_user()
    {
        $admin = $this->actingAsAdmin();
        $user = User::factory()->create();

        $updateData = [
            'name' => 'Updated Name',
            'phone' => '08987654321',
            'role' => 'trainer'
        ];

        $response = $this->putJson("/api/users/{$user->id}", $updateData);

        $this->assertApiResponse($response);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'phone' => '08987654321',
            'role' => 'trainer'
        ]);
    }

    /** @test */
    public function admin_can_update_user_membership_status()
    {
        $admin = $this->actingAsAdmin();
        $user = User::factory()->create(['membership_status' => 'inactive']);

        $response = $this->putJson("/api/users/{$user->id}", [
            'membership_status' => 'active',
            'membership_end_date' => now()->addDays(30)->format('Y-m-d')
        ]);

        $this->assertApiResponse($response);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'membership_status' => 'active'
        ]);
    }

    /** @test */
    public function admin_cannot_update_nonexistent_user()
    {
        $admin = $this->actingAsAdmin();

        $response = $this->putJson('/api/users/99999', [
            'name' => 'Updated Name'
        ]);

        $this->assertNotFound($response);
    }

    /** @test */
    public function admin_can_delete_user()
    {
        $admin = $this->actingAsAdmin();
        $user = User::factory()->create();

        $response = $this->deleteJson("/api/users/{$user->id}");

        $this->assertApiResponse($response);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /** @test */
    public function admin_cannot_delete_nonexistent_user()
    {
        $admin = $this->actingAsAdmin();

        $response = $this->deleteJson('/api/users/99999');

        $this->assertNotFound($response);
    }

    /** @test */
    public function admin_cannot_delete_themselves()
    {
        $admin = $this->actingAsAdmin();

        $response = $this->deleteJson("/api/users/{$admin->id}");

        $this->assertErrorResponse($response, 422);
        $response->assertJson([
            'success' => false,
            'message' => 'You cannot delete your own account'
        ]);
    }

    /** @test */
    public function admin_can_get_user_statistics()
    {
        $admin = $this->actingAsAdmin();
        
        // Create users with different roles and statuses
        User::factory()->count(3)->create(['role' => 'member']);
        User::factory()->count(2)->create(['role' => 'trainer']);
        User::factory()->count(2)->create(['membership_status' => 'active']);
        User::factory()->count(1)->create(['membership_status' => 'inactive']);

        $response = $this->getJson('/api/users/statistics');

        $this->assertApiResponse($response);
        $response->assertJsonStructure([
            'data' => [
                'total_users',
                'total_members',
                'total_trainers',
                'total_admins',
                'active_memberships',
                'inactive_memberships',
                'new_registrations_this_month'
            ]
        ]);
    }

    /** @test */
    public function member_cannot_access_user_statistics()
    {
        $member = $this->actingAsMember();

        $response = $this->getJson('/api/users/statistics');

        $this->assertForbidden($response);
    }

    /** @test */
    public function trainer_cannot_create_users()
    {
        $trainer = $this->actingAsTrainer();

        $userData = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '08123456789',
            'role' => 'member'
        ];

        $response = $this->postJson('/api/users', $userData);

        $this->assertForbidden($response);
    }

    /** @test */
    public function trainer_cannot_update_users()
    {
        $trainer = $this->actingAsTrainer();
        $user = User::factory()->create();

        $response = $this->putJson("/api/users/{$user->id}", [
            'name' => 'Updated Name'
        ]);

        $this->assertForbidden($response);
    }

    /** @test */
    public function trainer_cannot_delete_users()
    {
        $trainer = $this->actingAsTrainer();
        $user = User::factory()->create();

        $response = $this->deleteJson("/api/users/{$user->id}");

        $this->assertForbidden($response);
    }

    /** @test */
    public function super_admin_can_access_all_endpoints()
    {
        $superAdmin = $this->actingAsSuperAdmin();
        $user = User::factory()->create();

        // Test all CRUD operations
        $this->getJson('/api/users')->assertStatus(200);
        $this->getJson("/api/users/{$user->id}")->assertStatus(200);
        $this->getJson('/api/users/statistics')->assertStatus(200);
        
        $this->putJson("/api/users/{$user->id}", ['name' => 'Updated'])->assertStatus(200);
        $this->deleteJson("/api/users/{$user->id}")->assertStatus(200);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_user_endpoints()
    {
        $user = User::factory()->create();

        $this->getJson('/api/users')->assertStatus(401);
        $this->getJson("/api/users/{$user->id}")->assertStatus(401);
        $this->postJson('/api/users', [])->assertStatus(401);
        $this->putJson("/api/users/{$user->id}", [])->assertStatus(401);
        $this->deleteJson("/api/users/{$user->id}")->assertStatus(401);
        $this->getJson('/api/users/statistics')->assertStatus(401);
    }

    /** @test */
    public function admin_can_create_user_with_different_roles()
    {
        $admin = $this->actingAsAdmin();

        foreach (['member', 'trainer', 'admin'] as $role) {
            $userData = [
                'name' => "Test {$role}",
                'email' => "test{$role}@example.com",
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'phone' => '08123456789',
                'role' => $role
            ];

            $response = $this->postJson('/api/users', $userData);

            $this->assertApiResponse($response, 201);
            $this->assertDatabaseHas('users', [
                'email' => "test{$role}@example.com",
                'role' => $role
            ]);
        }
    }

    /** @test */
    public function admin_cannot_create_super_admin_user()
    {
        $admin = $this->actingAsAdmin();

        $userData = [
            'name' => 'Test Super Admin',
            'email' => 'testsuperadmin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '08123456789',
            'role' => 'super_admin'
        ];

        $response = $this->postJson('/api/users', $userData);

        $this->assertValidationError($response, ['role']);
    }

    /** @test */
    public function pagination_works_correctly()
    {
        $admin = $this->actingAsAdmin();
        User::factory()->count(25)->create();

        $response = $this->getJson('/api/users?per_page=10&page=2');

        $this->assertPaginatedResponse($response);
        $response->assertJsonFragment(['current_page' => 2]);
        $response->assertJsonFragment(['per_page' => 10]);
    }
}