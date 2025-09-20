<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;

abstract class ApiTestCase extends BaseTestCase
{
    use RefreshDatabase, WithFaker;

    protected $headers = [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations for testing
        $this->artisan('migrate');
        
        // Set default headers for API requests
        $this->withHeaders($this->headers);
    }

    /**
     * Create and authenticate a user with specific role
     */
    protected function actingAsUser(string $role = 'member', array $attributes = []): User
    {
        $user = User::factory()->create(array_merge([
            'role' => $role,
            'email_verified_at' => now(),
        ], $attributes));

        Sanctum::actingAs($user);

        return $user;
    }

    /**
     * Create authenticated member
     */
    protected function actingAsMember(array $attributes = []): User
    {
        return $this->actingAsUser('member', $attributes);
    }

    /**
     * Create authenticated trainer
     */
    protected function actingAsTrainer(array $attributes = []): User
    {
        return $this->actingAsUser('trainer', $attributes);
    }

    /**
     * Create authenticated admin
     */
    protected function actingAsAdmin(array $attributes = []): User
    {
        return $this->actingAsUser('admin', $attributes);
    }

    /**
     * Create authenticated super admin
     */
    protected function actingAsSuperAdmin(array $attributes = []): User
    {
        return $this->actingAsUser('super_admin', $attributes);
    }

    /**
     * Create unauthenticated user
     */
    protected function createUser(string $role = 'member', array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => $role,
            'email_verified_at' => now(),
        ], $attributes));
    }

    /**
     * Assert JSON response structure
     */
    protected function assertApiResponse($response, $status = 200, $hasData = true)
    {
        $response->assertStatus($status)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data'
                 ]);

        if ($hasData) {
            $response->assertJson(['success' => true]);
        }

        return $response;
    }

    /**
     * Assert paginated response structure
     */
    protected function assertPaginatedResponse($response, $status = 200)
    {
        $response->assertStatus($status)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'data',
                         'current_page',
                         'last_page',
                         'per_page',
                         'total'
                     ]
                 ]);

        return $response;
    }

    /**
     * Assert error response structure
     */
    protected function assertErrorResponse($response, $status = 422)
    {
        $response->assertStatus($status)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data'
                 ])
                 ->assertJson(['success' => false]);

        return $response;
    }

    /**
     * Assert validation error response
     */
    protected function assertValidationError($response, array $fields = [])
    {
        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'errors'
                     ]
                 ])
                 ->assertJson(['success' => false]);

        if (!empty($fields)) {
            foreach ($fields as $field) {
                $response->assertJsonValidationErrors($field);
            }
        }

        return $response;
    }

    /**
     * Assert unauthorized response
     */
    protected function assertUnauthorized($response)
    {
        return $this->assertErrorResponse($response, 401);
    }

    /**
     * Assert forbidden response
     */
    protected function assertForbidden($response)
    {
        return $this->assertErrorResponse($response, 403);
    }

    /**
     * Assert not found response
     */
    protected function assertNotFound($response)
    {
        return $this->assertErrorResponse($response, 404);
    }

    /**
     * Create test image file
     */
    protected function createTestImage($filename = 'test.jpg')
    {
        return \Illuminate\Http\Testing\File::image($filename, 100, 100);
    }

    /**
     * Mock Midtrans service
     */
    protected function mockMidtrans()
    {
        $mock = $this->createMock(\App\Services\PaymentService::class);
        $this->app->instance(\App\Services\PaymentService::class, $mock);
        return $mock;
    }
}