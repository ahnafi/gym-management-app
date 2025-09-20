<?php

namespace Tests\Feature\Api;

use Tests\ApiTestCase;
use App\Models\User;
use App\Models\MembershipPackage;
use App\Models\MembershipHistory;
use App\Models\Transaction;
use App\Services\AssignmentService;
use Illuminate\Support\Facades\Storage;

class MembershipControllerTest extends ApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function anyone_can_get_active_membership_packages()
    {
        MembershipPackage::factory()->count(3)->create(['is_active' => true]);
        MembershipPackage::factory()->count(2)->create(['is_active' => false]);

        $response = $this->getJson('/api/memberships/packages');

        $this->assertApiResponse($response);
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'price',
                    'duration_days',
                    'features'
                ]
            ]
        ]);
    }

    /** @test */
    public function admin_can_get_all_membership_packages_including_inactive()
    {
        $admin = $this->actingAsAdmin();
        MembershipPackage::factory()->count(3)->create(['is_active' => true]);
        MembershipPackage::factory()->count(2)->create(['is_active' => false]);

        $response = $this->getJson('/api/memberships/packages?include_inactive=true');

        $this->assertApiResponse($response);
        $response->assertJsonCount(5, 'data');
    }

    /** @test */
    public function admin_can_create_membership_package()
    {
        $admin = $this->actingAsAdmin();

        $packageData = [
            'name' => 'Premium Package',
            'description' => 'Premium membership with all features',
            'price' => 500000,
            'duration_days' => 90,
            'max_gym_visits' => 30,
            'max_gym_classes' => 15,
            'personal_trainer_sessions' => 8,
            'features' => ['Gym Access', 'Group Classes', 'Personal Training']
        ];

        $response = $this->postJson('/api/memberships/packages', $packageData);

        $this->assertApiResponse($response, 201);
        $this->assertDatabaseHas('membership_packages', [
            'name' => 'Premium Package',
            'price' => 500000,
            'duration_days' => 90
        ]);
    }

    /** @test */
    public function admin_can_create_package_with_image()
    {
        $admin = $this->actingAsAdmin();
        $image = $this->createTestImage('package.jpg');

        $packageData = [
            'name' => 'Premium Package',
            'description' => 'Premium membership',
            'price' => 500000,
            'duration_days' => 90,
            'image' => $image
        ];

        $response = $this->postJson('/api/memberships/packages', $packageData);

        $this->assertApiResponse($response, 201);
        Storage::assertExists('public/packages/' . $image->hashName());
    }

    /** @test */
    public function member_cannot_create_membership_package()
    {
        $member = $this->actingAsMember();

        $packageData = [
            'name' => 'Test Package',
            'price' => 100000,
            'duration_days' => 30
        ];

        $response = $this->postJson('/api/memberships/packages', $packageData);

        $this->assertForbidden($response);
    }

    /** @test */
    public function admin_can_update_membership_package()
    {
        $admin = $this->actingAsAdmin();
        $package = MembershipPackage::factory()->create();

        $updateData = [
            'name' => 'Updated Package Name',
            'price' => 750000,
            'is_active' => false
        ];

        $response = $this->putJson("/api/memberships/packages/{$package->id}", $updateData);

        $this->assertApiResponse($response);
        $this->assertDatabaseHas('membership_packages', [
            'id' => $package->id,
            'name' => 'Updated Package Name',
            'price' => 750000,
            'is_active' => false
        ]);
    }

    /** @test */
    public function admin_can_delete_membership_package()
    {
        $admin = $this->actingAsAdmin();
        $package = MembershipPackage::factory()->create();

        $response = $this->deleteJson("/api/memberships/packages/{$package->id}");

        $this->assertApiResponse($response);
        $this->assertDatabaseMissing('membership_packages', ['id' => $package->id]);
    }

    /** @test */
    public function authenticated_user_can_purchase_membership()
    {
        $user = $this->actingAsMember();
        $package = MembershipPackage::factory()->create(['price' => 500000]);
        
        $mock = $this->mockMidtrans();
        $mock->method('createTransaction')
             ->willReturn([
                 'transaction_id' => 'test_transaction_123',
                 'gross_amount' => 500000,
                 'payment_type' => 'credit_card'
             ]);

        $response = $this->postJson("/api/memberships/packages/{$package->id}/purchase");

        $this->assertApiResponse($response, 201);
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type' => 'membership',
            'item_id' => $package->id,
            'amount' => 500000,
            'status' => 'pending'
        ]);
    }

    /** @test */
    public function user_cannot_purchase_inactive_membership_package()
    {
        $user = $this->actingAsMember();
        $package = MembershipPackage::factory()->create(['is_active' => false]);

        $response = $this->postJson("/api/memberships/packages/{$package->id}/purchase");

        $this->assertErrorResponse($response, 422);
        $response->assertJson([
            'success' => false,
            'message' => 'This membership package is not available'
        ]);
    }

    /** @test */
    public function user_cannot_purchase_nonexistent_package()
    {
        $user = $this->actingAsMember();

        $response = $this->postJson('/api/memberships/packages/99999/purchase');

        $this->assertNotFound($response);
    }

    /** @test */
    public function authenticated_user_can_get_their_membership_history()
    {
        $user = $this->actingAsMember();
        $package = MembershipPackage::factory()->create();
        
        MembershipHistory::factory()->count(3)->create([
            'user_id' => $user->id,
            'membership_package_id' => $package->id
        ]);
        
        // Create history for other user (should not appear)
        $otherUser = User::factory()->create();
        MembershipHistory::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getJson('/api/memberships/history');

        $this->assertPaginatedResponse($response);
        $response->assertJsonCount(3, 'data.data');
        $response->assertJsonStructure([
            'data' => [
                'data' => [
                    '*' => [
                        'id',
                        'membership_code',
                        'start_date',
                        'end_date',
                        'status',
                        'membership_package'
                    ]
                ]
            ]
        ]);
    }

    /** @test */
    public function admin_can_get_all_membership_history()
    {
        $admin = $this->actingAsAdmin();
        MembershipHistory::factory()->count(5)->create();

        $response = $this->getJson('/api/memberships/history/all');

        $this->assertPaginatedResponse($response);
        $response->assertJsonCount(5, 'data.data');
    }

    /** @test */
    public function member_cannot_access_all_membership_history()
    {
        $member = $this->actingAsMember();

        $response = $this->getJson('/api/memberships/history/all');

        $this->assertForbidden($response);
    }

    /** @test */
    public function authenticated_user_can_get_current_membership()
    {
        $user = $this->actingAsMember([
            'membership_status' => 'active',
            'membership_end_date' => now()->addMonth()
        ]);
        
        $package = MembershipPackage::factory()->create();
        $membership = MembershipHistory::factory()->create([
            'user_id' => $user->id,
            'membership_package_id' => $package->id,
            'status' => 'active',
            'end_date' => now()->addMonth()
        ]);

        $response = $this->getJson('/api/memberships/current');

        $this->assertApiResponse($response);
        $response->assertJsonFragment([
            'membership_code' => $membership->membership_code,
            'status' => 'active'
        ]);
    }

    /** @test */
    public function user_with_no_active_membership_gets_null_current()
    {
        $user = $this->actingAsMember([
            'membership_status' => 'inactive'
        ]);

        $response = $this->getJson('/api/memberships/current');

        $this->assertApiResponse($response);
        $response->assertJson([
            'data' => null
        ]);
    }

    /** @test */
    public function admin_can_assign_membership_manually()
    {
        $admin = $this->actingAsAdmin();
        $user = User::factory()->create();
        $package = MembershipPackage::factory()->create();

        $mock = $this->createMock(AssignmentService::class);
        $mock->method('assignMembership')->willReturn(true);
        $this->app->instance(AssignmentService::class, $mock);

        $response = $this->postJson("/api/memberships/assign", [
            'user_id' => $user->id,
            'membership_package_id' => $package->id
        ]);

        $this->assertApiResponse($response);
    }

    /** @test */
    public function member_cannot_assign_membership_manually()
    {
        $member = $this->actingAsMember();
        $user = User::factory()->create();
        $package = MembershipPackage::factory()->create();

        $response = $this->postJson("/api/memberships/assign", [
            'user_id' => $user->id,
            'membership_package_id' => $package->id
        ]);

        $this->assertForbidden($response);
    }

    /** @test */
    public function admin_can_get_membership_statistics()
    {
        $admin = $this->actingAsAdmin();
        
        // Create test data
        MembershipPackage::factory()->count(3)->create();
        MembershipHistory::factory()->count(5)->create(['status' => 'active']);
        MembershipHistory::factory()->count(2)->create(['status' => 'expired']);
        
        $response = $this->getJson('/api/memberships/statistics');

        $this->assertApiResponse($response);
        $response->assertJsonStructure([
            'data' => [
                'total_packages',
                'active_packages',
                'total_memberships',
                'active_memberships',
                'expired_memberships',
                'revenue_this_month',
                'popular_packages'
            ]
        ]);
    }

    /** @test */
    public function package_creation_requires_valid_data()
    {
        $admin = $this->actingAsAdmin();

        $response = $this->postJson('/api/memberships/packages', []);

        $this->assertValidationError($response, ['name', 'price', 'duration_days']);
    }

    /** @test */
    public function package_price_must_be_positive()
    {
        $admin = $this->actingAsAdmin();

        $response = $this->postJson('/api/memberships/packages', [
            'name' => 'Test Package',
            'price' => -100,
            'duration_days' => 30
        ]);

        $this->assertValidationError($response, ['price']);
    }

    /** @test */
    public function package_duration_must_be_positive()
    {
        $admin = $this->actingAsAdmin();

        $response = $this->postJson('/api/memberships/packages', [
            'name' => 'Test Package',
            'price' => 100000,
            'duration_days' => 0
        ]);

        $this->assertValidationError($response, ['duration_days']);
    }

    /** @test */
    public function package_image_must_be_valid_image()
    {
        $admin = $this->actingAsAdmin();
        $file = \Illuminate\Http\UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->postJson('/api/memberships/packages', [
            'name' => 'Test Package',
            'price' => 100000,
            'duration_days' => 30,
            'image' => $file
        ]);

        $this->assertValidationError($response, ['image']);
    }

    /** @test */
    public function user_can_filter_packages_by_price_range()
    {
        MembershipPackage::factory()->create(['price' => 100000]);
        MembershipPackage::factory()->create(['price' => 500000]);
        MembershipPackage::factory()->create(['price' => 1000000]);

        $response = $this->getJson('/api/memberships/packages?min_price=200000&max_price=800000');

        $this->assertApiResponse($response);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['price' => 500000]);
    }

    /** @test */
    public function user_can_filter_packages_by_duration()
    {
        MembershipPackage::factory()->create(['duration_days' => 30]);
        MembershipPackage::factory()->create(['duration_days' => 90]);
        MembershipPackage::factory()->create(['duration_days' => 365]);

        $response = $this->getJson('/api/memberships/packages?duration=90');

        $this->assertApiResponse($response);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['duration_days' => 90]);
    }

    /** @test */
    public function unauthenticated_user_can_view_packages_but_cannot_purchase()
    {
        $package = MembershipPackage::factory()->create();

        // Can view packages
        $response = $this->getJson('/api/memberships/packages');
        $this->assertApiResponse($response);

        // Cannot purchase
        $response = $this->postJson("/api/memberships/packages/{$package->id}/purchase");
        $this->assertUnauthorized($response);
    }

    /** @test */
    public function admin_can_view_specific_package_details()
    {
        $admin = $this->actingAsAdmin();
        $package = MembershipPackage::factory()->create([
            'name' => 'Test Package',
            'description' => 'Test Description'
        ]);

        $response = $this->getJson("/api/memberships/packages/{$package->id}");

        $this->assertApiResponse($response);
        $response->assertJsonFragment([
            'name' => 'Test Package',
            'description' => 'Test Description'
        ]);
    }
}