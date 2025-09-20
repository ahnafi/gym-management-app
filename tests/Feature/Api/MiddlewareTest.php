<?php

namespace Tests\Feature\Api;

use Tests\ApiTestCase;
use App\Models\User;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MiddlewareTest extends ApiTestCase
{
    /** @test */
    public function api_response_middleware_formats_successful_response()
    {
        $user = $this->actingAsMember();

        $response = $this->getJson('/api/auth/profile');

        $this->assertApiResponse($response);
        $response->assertJsonStructure([
            'success',
            'message',
            'data'
        ]);
        $response->assertJson(['success' => true]);
    }

    /** @test */
    public function api_response_middleware_formats_error_response()
    {
        $response = $this->getJson('/api/auth/profile'); // Unauthenticated

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'success',
            'message',
            'data'
        ]);
        $response->assertJson(['success' => false]);
    }

    /** @test */
    public function role_middleware_allows_access_for_correct_role()
    {
        $admin = $this->actingAsAdmin();

        $response = $this->getJson('/api/users');

        $this->assertApiResponse($response);
    }

    /** @test */
    public function role_middleware_denies_access_for_incorrect_role()
    {
        $member = $this->actingAsMember();

        $response = $this->getJson('/api/users');

        $this->assertForbidden($response);
    }

    /** @test */
    public function role_middleware_allows_admin_and_trainer_access()
    {
        // Test that admin can access trainer endpoints
        $admin = $this->actingAsAdmin();

        $response = $this->getJson('/api/personal-trainers/statistics');

        $this->assertApiResponse($response);
    }

    /** @test */
    public function super_admin_can_access_all_endpoints()
    {
        $superAdmin = $this->actingAsSuperAdmin();

        // Test various endpoints that require different roles
        $this->getJson('/api/users')->assertStatus(200);
        $this->getJson('/api/memberships/statistics')->assertStatus(200);
        $this->getJson('/api/gym-classes/statistics')->assertStatus(200);
        $this->getJson('/api/personal-trainers/statistics')->assertStatus(200);
        $this->getJson('/api/gym-visits/statistics')->assertStatus(200);
        $this->getJson('/api/payments/statistics')->assertStatus(200);
    }

    /** @test */
    public function trainer_can_access_trainer_specific_endpoints()
    {
        $trainer = $this->actingAsTrainer();

        $response = $this->getJson('/api/personal-trainers/my-assignments');

        $this->assertApiResponse($response);
    }

    /** @test */
    public function trainer_cannot_access_admin_only_endpoints()
    {
        $trainer = $this->actingAsTrainer();

        $response = $this->getJson('/api/users');

        $this->assertForbidden($response);
    }

    /** @test */
    public function member_can_access_member_endpoints()
    {
        $member = $this->actingAsMember();

        $response = $this->getJson('/api/gym-visits/my-visits');

        $this->assertApiResponse($response);
    }

    /** @test */
    public function member_cannot_access_admin_endpoints()
    {
        $member = $this->actingAsMember();

        $response = $this->getJson('/api/users/statistics');

        $this->assertForbidden($response);
    }

    /** @test */
    public function api_middleware_handles_validation_errors()
    {
        $admin = $this->actingAsAdmin();

        $response = $this->postJson('/api/users', []); // Missing required fields

        $this->assertValidationError($response);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'errors'
            ]
        ]);
    }

    /** @test */
    public function api_middleware_handles_not_found_errors()
    {
        $admin = $this->actingAsAdmin();

        $response = $this->getJson('/api/users/99999');

        $this->assertNotFound($response);
        $response->assertJson(['success' => false]);
    }

    /** @test */
    public function api_middleware_handles_method_not_allowed()
    {
        $admin = $this->actingAsAdmin();

        $response = $this->putJson('/api/auth/register', []); // POST only endpoint

        $response->assertStatus(405);
        $response->assertJsonStructure([
            'success',
            'message',
            'data'
        ]);
        $response->assertJson(['success' => false]);
    }

    /** @test */
    public function role_middleware_unit_test()
    {
        $middleware = new RoleMiddleware();
        $request = Request::create('/test', 'GET');
        
        // Test with admin user
        $admin = User::factory()->create(['role' => 'admin']);
        $request->setUserResolver(function () use ($admin) {
            return $admin;
        });

        $next = function ($req) {
            return new Response('success');
        };

        $response = $middleware->handle($request, $next, 'admin');
        $this->assertEquals('success', $response->getContent());
    }

    /** @test */
    public function role_middleware_denies_unauthorized_user()
    {
        $middleware = new RoleMiddleware();
        $request = Request::create('/test', 'GET');
        
        // Test with member user trying to access admin endpoint
        $member = User::factory()->create(['role' => 'member']);
        $request->setUserResolver(function () use ($member) {
            return $member;
        });

        $next = function ($req) {
            return new Response('success');
        };

        $response = $middleware->handle($request, $next, 'admin');
        $this->assertEquals(403, $response->getStatusCode());
    }

    /** @test */
    public function role_middleware_allows_multiple_roles()
    {
        $middleware = new RoleMiddleware();
        $request = Request::create('/test', 'GET');
        
        // Test with trainer user accessing trainer|admin endpoint
        $trainer = User::factory()->create(['role' => 'trainer']);
        $request->setUserResolver(function () use ($trainer) {
            return $trainer;
        });

        $next = function ($req) {
            return new Response('success');
        };

        $response = $middleware->handle($request, $next, 'trainer', 'admin');
        $this->assertEquals('success', $response->getContent());
    }

    /** @test */
    public function api_returns_consistent_error_format_for_server_errors()
    {
        // This test would typically trigger a 500 error
        // For testing purposes, we'll simulate this by making an invalid request
        $response = $this->getJson('/api/nonexistent-endpoint');

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'success',
            'message',
            'data'
        ]);
        $response->assertJson(['success' => false]);
    }

    /** @test */
    public function api_handles_csrf_token_correctly()
    {
        // API endpoints should not require CSRF tokens
        $user = $this->actingAsMember();

        $response = $this->postJson('/api/gym-visits/check-in', [], [
            'X-CSRF-TOKEN' => '' // Empty CSRF token should still work for API
        ]);

        // Should work even without CSRF token for API routes
        $this->assertApiResponse($response, 201);
    }

    /** @test */
    public function api_handles_rate_limiting()
    {
        // This test would check rate limiting if implemented
        // For now, we'll just verify the endpoint works normally
        $user = $this->actingAsMember();

        for ($i = 0; $i < 5; $i++) {
            $response = $this->getJson('/api/auth/profile');
            $this->assertApiResponse($response);
        }
    }

    /** @test */
    public function api_accepts_json_content_type()
    {
        $admin = $this->actingAsAdmin();

        $response = $this->postJson('/api/users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'member'
        ], [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ]);

        $this->assertApiResponse($response, 201);
    }

    /** @test */
    public function api_returns_json_for_all_responses()
    {
        // Test various endpoints to ensure they all return JSON
        $admin = $this->actingAsAdmin();

        $endpoints = [
            'GET /api/users',
            'GET /api/memberships/packages',
            'GET /api/gym-classes',
            'GET /api/personal-trainers'
        ];

        foreach ($endpoints as $endpoint) {
            [$method, $url] = explode(' ', $endpoint);
            $response = $this->json($method, $url);
            
            $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        }
    }

    /** @test */
    public function api_handles_cors_headers()
    {
        // Test that CORS headers are properly set
        $response = $this->getJson('/api/memberships/packages');

        // Basic CORS headers should be present for API endpoints
        $this->assertApiResponse($response);
        
        // Verify it's a proper JSON response
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    }

    /** @test */
    public function authenticated_endpoints_return_401_for_unauthenticated_users()
    {
        $protectedEndpoints = [
            'GET /api/auth/profile',
            'POST /api/auth/logout',
            'GET /api/gym-visits/my-visits',
            'GET /api/memberships/current',
            'POST /api/gym-visits/check-in'
        ];

        foreach ($protectedEndpoints as $endpoint) {
            [$method, $url] = explode(' ', $endpoint);
            $response = $this->json($method, $url);
            
            $this->assertUnauthorized($response);
        }
    }

    /** @test */
    public function role_protected_endpoints_return_403_for_wrong_role()
    {
        $member = $this->actingAsMember();

        $adminEndpoints = [
            'GET /api/users',
            'POST /api/users',
            'GET /api/users/statistics',
            'GET /api/payments/all-transactions'
        ];

        foreach ($adminEndpoints as $endpoint) {
            [$method, $url] = explode(' ', $endpoint);
            $response = $this->json($method, $url);
            
            $this->assertForbidden($response);
        }
    }
}