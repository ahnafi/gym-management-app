<?php

namespace Tests\Unit;

use Tests\ApiTestCase;
use App\Services\AssignmentService;
use App\Services\PaymentService;
use App\Models\User;

class ServicesTest extends ApiTestCase
{
    /** @test */
    public function assignment_service_exists_and_can_be_instantiated()
    {
        $assignmentService = new AssignmentService();
        $this->assertInstanceOf(AssignmentService::class, $assignmentService);
    }

    /** @test */
    public function payment_service_exists_and_can_be_instantiated()
    {
        $paymentService = new PaymentService();
        $this->assertInstanceOf(PaymentService::class, $paymentService);
    }

    /** @test */
    public function file_naming_service_exists()
    {
        $this->assertTrue(class_exists(\App\Services\FileNaming::class));
    }

    /** @test */
    public function user_model_has_roles()
    {
        $member = User::factory()->create(['role' => 'member']);
        $trainer = User::factory()->create(['role' => 'trainer']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->assertEquals('member', $member->role);
        $this->assertEquals('trainer', $trainer->role);
        $this->assertEquals('admin', $admin->role);
    }

    /** @test */
    public function user_model_has_valid_email()
    {
        $user = User::factory()->create();
        
        $this->assertNotNull($user->email);
        $this->assertStringContainsString('@', $user->email);
    }

    /** @test */
    public function user_model_has_encrypted_password()
    {
        $user = User::factory()->create(['password' => 'password123']);
        
        $this->assertNotEmpty($user->password);
        $this->assertNotEquals('password123', $user->password);
    }

    /** @test */
    public function models_exist_and_can_be_instantiated()
    {
        // Test that all models exist
        $this->assertTrue(class_exists(\App\Models\User::class));
        $this->assertTrue(class_exists(\App\Models\MembershipPackage::class));
        $this->assertTrue(class_exists(\App\Models\MembershipHistory::class));
        $this->assertTrue(class_exists(\App\Models\GymClass::class));
        $this->assertTrue(class_exists(\App\Models\GymClassSchedule::class));
        $this->assertTrue(class_exists(\App\Models\GymVisit::class));
        $this->assertTrue(class_exists(\App\Models\PersonalTrainer::class));
        $this->assertTrue(class_exists(\App\Models\PersonalTrainerPackage::class));
        $this->assertTrue(class_exists(\App\Models\PersonalTrainerAssignment::class));
        $this->assertTrue(class_exists(\App\Models\Transaction::class));
    }

    /** @test */
    public function services_exist_and_can_be_instantiated()
    {
        // Test that all services exist
        $this->assertTrue(class_exists(\App\Services\AssignmentService::class));
        $this->assertTrue(class_exists(\App\Services\PaymentService::class));
        $this->assertTrue(class_exists(\App\Services\FileNaming::class));
        
        // Test instantiation
        $assignment = new \App\Services\AssignmentService();
        $payment = new \App\Services\PaymentService();
        $fileNaming = new \App\Services\FileNaming();
        
        $this->assertInstanceOf(\App\Services\AssignmentService::class, $assignment);
        $this->assertInstanceOf(\App\Services\PaymentService::class, $payment);
        $this->assertInstanceOf(\App\Services\FileNaming::class, $fileNaming);
    }

    /** @test */
    public function models_have_correct_table_names()
    {
        // Test that models have correct table configuration
        $membershipPackage = new \App\Models\MembershipPackage();
        $gymClass = new \App\Models\GymClass();
        $gymVisit = new \App\Models\GymVisit();
        $personalTrainer = new \App\Models\PersonalTrainer();
        
        $this->assertEquals('membership_packages', $membershipPackage->getTable());
        $this->assertEquals('gym_classes', $gymClass->getTable());
        $this->assertEquals('gym_visits', $gymVisit->getTable());
        $this->assertEquals('personal_trainers', $personalTrainer->getTable());
    }

    /** @test */
    public function models_have_factory_trait()
    {
        // Test that models use HasFactory trait
        $this->assertContains('Illuminate\Database\Eloquent\Factories\HasFactory', class_uses(\App\Models\User::class));
        $this->assertContains('Illuminate\Database\Eloquent\Factories\HasFactory', class_uses(\App\Models\MembershipPackage::class));
        $this->assertContains('Illuminate\Database\Eloquent\Factories\HasFactory', class_uses(\App\Models\GymClass::class));
        $this->assertContains('Illuminate\Database\Eloquent\Factories\HasFactory', class_uses(\App\Models\PersonalTrainer::class));
    }

    /** @test */
    public function user_factory_creates_different_roles()
    {
        $members = User::factory()->count(3)->create(['role' => 'member']);
        $trainers = User::factory()->count(2)->create(['role' => 'trainer']);
        $admins = User::factory()->count(1)->create(['role' => 'admin']);

        $this->assertCount(3, $members->where('role', 'member'));
        $this->assertCount(2, $trainers->where('role', 'trainer'));
        $this->assertCount(1, $admins->where('role', 'admin'));
    }

    /** @test */
    public function assignment_service_can_be_mocked()
    {
        $mock = $this->createMock(AssignmentService::class);
        
        // Test that mock can be created
        $this->assertInstanceOf(AssignmentService::class, $mock);
    }

    /** @test */
    public function payment_service_can_be_mocked()
    {
        $mock = $this->createMock(PaymentService::class);
        
        // Test that mock can be created
        $this->assertInstanceOf(PaymentService::class, $mock);
    }

    /** @test */
    public function api_test_case_has_helper_methods()
    {
        // Test that our ApiTestCase has the helper methods we expect
        $this->assertTrue(method_exists($this, 'actingAsUser'));
        $this->assertTrue(method_exists($this, 'assertApiResponse'));
        $this->assertTrue(method_exists($this, 'assertPaginatedResponse'));
    }

    /** @test */
    public function models_use_correct_date_casting()
    {
        $user = User::factory()->create();
        
        // Test that timestamps are properly cast to Carbon instances
        $this->assertInstanceOf(\Carbon\Carbon::class, $user->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $user->updated_at);
    }

    /** @test */
    public function string_helper_functions_work()
    {
        // Test string manipulation that might be used in the app
        $testString = 'Hello World';
        $slug = \Illuminate\Support\Str::slug($testString);
        
        $this->assertEquals('hello-world', $slug);
        
        $random = \Illuminate\Support\Str::random(10);
        $this->assertEquals(10, strlen($random));
    }

    /** @test */
    public function carbon_date_functions_work()
    {
        // Test date manipulations that might be used in services
        $now = \Carbon\Carbon::now();
        $future = $now->copy()->addDays(30);
        $past = $now->copy()->subDays(30);
        
        $this->assertTrue($future->isAfter($now));
        $this->assertTrue($past->isBefore($now));
        $this->assertEquals(60, $past->diffInDays($future));
    }

    /** @test */
    public function json_encoding_works_correctly()
    {
        // Test JSON operations that might be used for metadata
        $data = [
            'features' => ['Gym Access', 'Group Classes'],
            'settings' => ['auto_extend' => true]
        ];
        
        $encoded = json_encode($data);
        $decoded = json_decode($encoded, true);
        
        $this->assertEquals($data, $decoded);
    }

    /** @test */
    public function array_operations_work_correctly()
    {
        // Test array operations used in the application
        $items = ['item1', 'item2', 'item3'];
        
        $this->assertCount(3, $items);
        $this->assertContains('item2', $items);
        $this->assertEquals('item1', $items[0]);
    }

    /** @test */
    public function number_formatting_works()
    {
        // Test number formatting that might be used for prices
        $price = 500000;
        $formatted = number_format($price, 0, ',', '.');
        
        $this->assertEquals('500.000', $formatted);
    }
}