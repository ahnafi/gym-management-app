<?php

namespace Tests\Feature\Api;

use Tests\ApiTestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;

class AuthControllerTest extends ApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Mail::fake();
    }

    /** @test */
    public function user_can_register_successfully()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '08123456789',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $this->assertApiResponse($response, 201);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'email',
                    'phone',
                    'role',
                    'membership_registered',
                    'membership_status'
                ],
                'token'
            ]
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'name' => 'John Doe',
            'phone' => '08123456789',
            'role' => 'member'
        ]);
    }

    /** @test */
    public function registration_requires_valid_data()
    {
        $response = $this->postJson('/api/auth/register', []);

        $this->assertValidationError($response, ['name', 'email', 'password']);
    }

    /** @test */
    public function registration_requires_unique_email()
    {
        $existingUser = User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '08123456789',
        ]);

        $this->assertValidationError($response, ['email']);
    }

    /** @test */
    public function registration_requires_password_confirmation()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different_password',
            'phone' => '08123456789',
        ]);

        $this->assertValidationError($response, ['password']);
    }

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now()
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $this->assertApiResponse($response);
        $response->assertJsonStructure([
            'success',
            'message', 
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'email',
                    'role'
                ],
                'token'
            ]
        ]);
    }

    /** @test */
    public function login_fails_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123')
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'wrong_password',
        ]);

        $this->assertErrorResponse($response, 401);
        $response->assertJson([
            'success' => false,
            'message' => 'Invalid credentials'
        ]);
    }

    /** @test */
    public function login_fails_with_unverified_email()
    {
        $user = User::factory()->unverified()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123')
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $this->assertErrorResponse($response, 401);
        $response->assertJson([
            'success' => false,
            'message' => 'Please verify your email before logging in'
        ]);
    }

    /** @test */
    public function authenticated_user_can_logout()
    {
        $user = $this->actingAsMember();

        $response = $this->postJson('/api/auth/logout');

        $this->assertApiResponse($response);
        $response->assertJson([
            'success' => true,
            'message' => 'Successfully logged out'
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_logout()
    {
        $response = $this->postJson('/api/auth/logout');

        $this->assertUnauthorized($response);
    }

    /** @test */
    public function authenticated_user_can_get_profile()
    {
        $user = $this->actingAsMember([
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);

        $response = $this->getJson('/api/auth/profile');

        $this->assertApiResponse($response);
        $response->assertJsonFragment([
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_get_profile()
    {
        $response = $this->getJson('/api/auth/profile');

        $this->assertUnauthorized($response);
    }

    /** @test */
    public function authenticated_user_can_update_profile()
    {
        $user = $this->actingAsMember();

        $updateData = [
            'name' => 'Updated Name',
            'phone' => '08987654321',
            'profile_bio' => 'Updated bio'
        ];

        $response = $this->putJson('/api/auth/profile', $updateData);

        $this->assertApiResponse($response);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'phone' => '08987654321',
            'profile_bio' => 'Updated bio'
        ]);
    }

    /** @test */
    public function user_can_update_profile_with_image()
    {
        $user = $this->actingAsMember();
        $image = $this->createTestImage('profile.jpg');

        $response = $this->postJson('/api/auth/profile', [
            'name' => 'Updated Name',
            'profile_image' => $image
        ]);

        $this->assertApiResponse($response);
        Storage::assertExists('public/profiles/' . $image->hashName());
    }

    /** @test */
    public function authenticated_user_can_change_password()
    {
        $user = $this->actingAsMember([
            'password' => Hash::make('old_password')
        ]);

        $response = $this->putJson('/api/auth/change-password', [
            'current_password' => 'old_password',
            'new_password' => 'new_password123',
            'new_password_confirmation' => 'new_password123'
        ]);

        $this->assertApiResponse($response);
        
        $user->refresh();
        $this->assertTrue(Hash::check('new_password123', $user->password));
    }

    /** @test */
    public function change_password_requires_correct_current_password()
    {
        $user = $this->actingAsMember([
            'password' => Hash::make('old_password')
        ]);

        $response = $this->putJson('/api/auth/change-password', [
            'current_password' => 'wrong_password',
            'new_password' => 'new_password123',
            'new_password_confirmation' => 'new_password123'
        ]);

        $this->assertValidationError($response, ['current_password']);
    }

    /** @test */
    public function change_password_requires_password_confirmation()
    {
        $user = $this->actingAsMember([
            'password' => Hash::make('old_password')
        ]);

        $response = $this->putJson('/api/auth/change-password', [
            'current_password' => 'old_password',
            'new_password' => 'new_password123',
            'new_password_confirmation' => 'different_password'
        ]);

        $this->assertValidationError($response, ['new_password']);
    }

    /** @test */
    public function user_can_request_email_verification()
    {
        $user = $this->actingAsMember(['email_verified_at' => null]);

        $response = $this->postJson('/api/auth/email/verification-notification');

        $this->assertApiResponse($response);
        Mail::assertSent(\Illuminate\Auth\Notifications\VerifyEmail::class);
    }

    /** @test */
    public function verified_user_cannot_request_verification_again()
    {
        $user = $this->actingAsMember(['email_verified_at' => now()]);

        $response = $this->postJson('/api/auth/email/verification-notification');

        $this->assertErrorResponse($response, 400);
        $response->assertJson([
            'success' => false,
            'message' => 'Email already verified'
        ]);
    }

    /** @test */
    public function user_can_verify_email_with_valid_link()
    {
        Event::fake();
        
        $user = $this->actingAsMember(['email_verified_at' => null]);
        
        $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->getJson($verificationUrl);

        $this->assertApiResponse($response);
        
        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        
        Event::assertDispatched(Verified::class);
    }

    /** @test */
    public function email_verification_fails_with_invalid_signature()
    {
        $user = $this->actingAsMember(['email_verified_at' => null]);

        $response = $this->getJson("/api/auth/email/verify/{$user->id}/invalid_hash");

        $this->assertErrorResponse($response, 403);
    }

    /** @test */
    public function already_verified_user_gets_appropriate_message()
    {
        $user = $this->actingAsMember(['email_verified_at' => now()]);
        
        $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->getJson($verificationUrl);

        $this->assertApiResponse($response);
        $response->assertJson([
            'success' => true,
            'message' => 'Email already verified'
        ]);
    }

    /** @test */
    public function profile_update_validates_email_uniqueness()
    {
        $existingUser = User::factory()->create(['email' => 'existing@example.com']);
        $user = $this->actingAsMember();

        $response = $this->putJson('/api/auth/profile', [
            'email' => 'existing@example.com'
        ]);

        $this->assertValidationError($response, ['email']);
    }

    /** @test */
    public function user_can_keep_same_email_when_updating_profile()
    {
        $user = $this->actingAsMember(['email' => 'john@example.com']);

        $response = $this->putJson('/api/auth/profile', [
            'name' => 'Updated Name',
            'email' => 'john@example.com'
        ]);

        $this->assertApiResponse($response);
    }

    /** @test */
    public function profile_image_must_be_valid_image_file()
    {
        $user = $this->actingAsMember();
        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->postJson('/api/auth/profile', [
            'profile_image' => $file
        ]);

        $this->assertValidationError($response, ['profile_image']);
    }

    /** @test */
    public function profile_image_has_size_limit()
    {
        $user = $this->actingAsMember();
        $largeImage = UploadedFile::fake()->image('large.jpg')->size(10240); // 10MB

        $response = $this->postJson('/api/auth/profile', [
            'profile_image' => $largeImage
        ]);

        $this->assertValidationError($response, ['profile_image']);
    }
}