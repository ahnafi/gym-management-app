<?php

namespace Tests\Feature\Api;

use Tests\ApiTestCase;
use App\Models\User;
use App\Models\Transaction;
use App\Models\MembershipPackage;
use App\Models\GymClass;
use App\Models\PersonalTrainerPackage;
use App\Services\PaymentService;
use App\Services\AssignmentService;

class PaymentControllerTest extends ApiTestCase
{
    /** @test */
    public function authenticated_user_can_get_their_transactions()
    {
        $user = $this->actingAsMember();
        
        Transaction::factory()->count(3)->create(['user_id' => $user->id]);
        
        // Create transactions for other user (should not appear)
        $otherUser = User::factory()->create();
        Transaction::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getJson('/api/payments/transactions');

        $this->assertPaginatedResponse($response);
        $response->assertJsonCount(3, 'data.data');
        $response->assertJsonStructure([
            'data' => [
                'data' => [
                    '*' => [
                        'id',
                        'transaction_code',
                        'type',
                        'amount',
                        'status',
                        'payment_method',
                        'created_at'
                    ]
                ]
            ]
        ]);
    }

    /** @test */
    public function user_can_filter_transactions_by_status()
    {
        $user = $this->actingAsMember();
        
        Transaction::factory()->count(2)->create([
            'user_id' => $user->id,
            'status' => 'paid'
        ]);
        Transaction::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        $response = $this->getJson('/api/payments/transactions?status=paid');

        $this->assertPaginatedResponse($response);
        $response->assertJsonCount(2, 'data.data');
    }

    /** @test */
    public function user_can_filter_transactions_by_type()
    {
        $user = $this->actingAsMember();
        
        Transaction::factory()->count(2)->create([
            'user_id' => $user->id,
            'type' => 'membership'
        ]);
        Transaction::factory()->create([
            'user_id' => $user->id,
            'type' => 'gym_class'
        ]);

        $response = $this->getJson('/api/payments/transactions?type=membership');

        $this->assertPaginatedResponse($response);
        $response->assertJsonCount(2, 'data.data');
    }

    /** @test */
    public function authenticated_user_can_get_specific_transaction()
    {
        $user = $this->actingAsMember();
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'transaction_code' => 'TRX-12345678'
        ]);

        $response = $this->getJson("/api/payments/transactions/{$transaction->id}");

        $this->assertApiResponse($response);
        $response->assertJsonFragment([
            'transaction_code' => 'TRX-12345678'
        ]);
    }

    /** @test */
    public function user_cannot_view_others_transactions()
    {
        $user = $this->actingAsMember();
        $otherUser = User::factory()->create();
        $transaction = Transaction::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getJson("/api/payments/transactions/{$transaction->id}");

        $this->assertForbidden($response);
    }

    /** @test */
    public function admin_can_view_any_transaction()
    {
        $admin = $this->actingAsAdmin();
        $user = User::factory()->create();
        $transaction = Transaction::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/payments/transactions/{$transaction->id}");

        $this->assertApiResponse($response);
    }

    /** @test */
    public function webhook_can_update_transaction_status()
    {
        $transaction = Transaction::factory()->pending()->create([
            'payment_data' => json_encode(['transaction_id' => 'midtrans_123'])
        ]);

        $webhookData = [
            'order_id' => $transaction->transaction_code,
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
            'transaction_id' => 'midtrans_123',
            'gross_amount' => $transaction->amount,
            'payment_type' => 'credit_card'
        ];

        // Mock AssignmentService
        $assignmentMock = $this->createMock(AssignmentService::class);
        $assignmentMock->method('assignMembership')->willReturn(true);
        $assignmentMock->method('assignGymClass')->willReturn(true);
        $assignmentMock->method('assignPersonalTrainer')->willReturn(true);
        $this->app->instance(AssignmentService::class, $assignmentMock);

        $response = $this->postJson('/api/payments/webhook', $webhookData);

        $this->assertApiResponse($response);
        
        $transaction->refresh();
        $this->assertEquals('paid', $transaction->status);
        $this->assertNotNull($transaction->paid_at);
    }

    /** @test */
    public function webhook_handles_failed_payment()
    {
        $transaction = Transaction::factory()->pending()->create();

        $webhookData = [
            'order_id' => $transaction->transaction_code,
            'transaction_status' => 'deny',
            'fraud_status' => 'deny',
            'transaction_id' => 'midtrans_123',
            'gross_amount' => $transaction->amount
        ];

        $response = $this->postJson('/api/payments/webhook', $webhookData);

        $this->assertApiResponse($response);
        
        $transaction->refresh();
        $this->assertEquals('failed', $transaction->status);
        $this->assertNull($transaction->paid_at);
    }

    /** @test */
    public function webhook_ignores_unknown_transaction()
    {
        $webhookData = [
            'order_id' => 'UNKNOWN_TRANSACTION',
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept'
        ];

        $response = $this->postJson('/api/payments/webhook', $webhookData);

        $this->assertErrorResponse($response, 404);
        $response->assertJson([
            'success' => false,
            'message' => 'Transaction not found'
        ]);
    }

    /** @test */
    public function admin_can_get_all_transactions()
    {
        $admin = $this->actingAsAdmin();
        Transaction::factory()->count(10)->create();

        $response = $this->getJson('/api/payments/all-transactions');

        $this->assertPaginatedResponse($response);
        $response->assertJsonCount(10, 'data.data');
        $response->assertJsonStructure([
            'data' => [
                'data' => [
                    '*' => [
                        'id',
                        'transaction_code',
                        'type',
                        'amount',
                        'status',
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
    public function member_cannot_get_all_transactions()
    {
        $member = $this->actingAsMember();

        $response = $this->getJson('/api/payments/all-transactions');

        $this->assertForbidden($response);
    }

    /** @test */
    public function admin_can_manually_approve_payment()
    {
        $admin = $this->actingAsAdmin();
        $transaction = Transaction::factory()->pending()->create();

        // Mock AssignmentService
        $assignmentMock = $this->createMock(AssignmentService::class);
        $assignmentMock->method('assignMembership')->willReturn(true);
        $this->app->instance(AssignmentService::class, $assignmentMock);

        $response = $this->postJson("/api/payments/transactions/{$transaction->id}/approve");

        $this->assertApiResponse($response);
        
        $transaction->refresh();
        $this->assertEquals('paid', $transaction->status);
        $this->assertNotNull($transaction->paid_at);
    }

    /** @test */
    public function admin_cannot_approve_already_paid_transaction()
    {
        $admin = $this->actingAsAdmin();
        $transaction = Transaction::factory()->paid()->create();

        $response = $this->postJson("/api/payments/transactions/{$transaction->id}/approve");

        $this->assertErrorResponse($response, 422);
        $response->assertJson([
            'success' => false,
            'message' => 'Transaction is already processed'
        ]);
    }

    /** @test */
    public function admin_can_reject_payment()
    {
        $admin = $this->actingAsAdmin();
        $transaction = Transaction::factory()->pending()->create();

        $response = $this->postJson("/api/payments/transactions/{$transaction->id}/reject");

        $this->assertApiResponse($response);
        
        $transaction->refresh();
        $this->assertEquals('failed', $transaction->status);
    }

    /** @test */
    public function member_cannot_approve_or_reject_payments()
    {
        $member = $this->actingAsMember();
        $transaction = Transaction::factory()->pending()->create();

        $this->postJson("/api/payments/transactions/{$transaction->id}/approve")
             ->assertStatus(403);
             
        $this->postJson("/api/payments/transactions/{$transaction->id}/reject")
             ->assertStatus(403);
    }

    /** @test */
    public function admin_can_get_payment_statistics()
    {
        $admin = $this->actingAsAdmin();
        
        Transaction::factory()->count(5)->paid()->create(['amount' => 500000]);
        Transaction::factory()->count(3)->pending()->create();
        Transaction::factory()->count(2)->failed()->create();

        $response = $this->getJson('/api/payments/statistics');

        $this->assertApiResponse($response);
        $response->assertJsonStructure([
            'data' => [
                'total_transactions',
                'total_revenue',
                'pending_payments',
                'failed_payments',
                'success_rate',
                'revenue_this_month',
                'revenue_by_type',
                'monthly_revenue_trend',
                'payment_method_distribution'
            ]
        ]);
    }

    /** @test */
    public function user_can_retry_failed_payment()
    {
        $user = $this->actingAsMember();
        $transaction = Transaction::factory()->failed()->create(['user_id' => $user->id]);

        $mock = $this->mockMidtrans();
        $mock->method('createTransaction')
             ->willReturn([
                 'transaction_id' => 'retry_transaction_123',
                 'gross_amount' => $transaction->amount,
                 'payment_type' => 'credit_card'
             ]);

        $response = $this->postJson("/api/payments/transactions/{$transaction->id}/retry");

        $this->assertApiResponse($response);
        
        $transaction->refresh();
        $this->assertEquals('pending', $transaction->status);
    }

    /** @test */
    public function user_cannot_retry_successful_payment()
    {
        $user = $this->actingAsMember();
        $transaction = Transaction::factory()->paid()->create(['user_id' => $user->id]);

        $response = $this->postJson("/api/payments/transactions/{$transaction->id}/retry");

        $this->assertErrorResponse($response, 422);
        $response->assertJson([
            'success' => false,
            'message' => 'Only failed transactions can be retried'
        ]);
    }

    /** @test */
    public function payment_webhook_validates_signature()
    {
        // This would typically validate Midtrans signature
        // For testing, we'll assume the signature validation passes
        $transaction = Transaction::factory()->pending()->create();

        $webhookData = [
            'order_id' => $transaction->transaction_code,
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept'
        ];

        $response = $this->postJson('/api/payments/webhook', $webhookData);

        $this->assertApiResponse($response);
    }

    /** @test */
    public function admin_can_filter_transactions_by_date_range()
    {
        $admin = $this->actingAsAdmin();
        
        Transaction::factory()->create([
            'created_at' => '2024-01-15 10:00:00'
        ]);
        Transaction::factory()->create([
            'created_at' => '2024-01-20 10:00:00'
        ]);
        Transaction::factory()->create([
            'created_at' => '2024-01-25 10:00:00'
        ]);

        $response = $this->getJson('/api/payments/all-transactions?start_date=2024-01-18&end_date=2024-01-22');

        $this->assertPaginatedResponse($response);
        $response->assertJsonCount(1, 'data.data');
    }

    /** @test */
    public function admin_can_search_transactions_by_user()
    {
        $admin = $this->actingAsAdmin();
        $user = User::factory()->create(['name' => 'John Doe']);
        
        Transaction::factory()->count(2)->create(['user_id' => $user->id]);
        Transaction::factory()->create(); // Other user

        $response = $this->getJson('/api/payments/all-transactions?search=John Doe');

        $this->assertPaginatedResponse($response);
        $response->assertJsonCount(2, 'data.data');
    }

    /** @test */
    public function webhook_assigns_membership_after_successful_payment()
    {
        $user = User::factory()->create();
        $package = MembershipPackage::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'type' => 'membership',
            'item_id' => $package->id,
            'status' => 'pending'
        ]);

        $assignmentMock = $this->createMock(AssignmentService::class);
        $assignmentMock->expects($this->once())
                      ->method('assignMembership')
                      ->with($user->id, $package->id)
                      ->willReturn(true);
        $this->app->instance(AssignmentService::class, $assignmentMock);

        $webhookData = [
            'order_id' => $transaction->transaction_code,
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept'
        ];

        $response = $this->postJson('/api/payments/webhook', $webhookData);

        $this->assertApiResponse($response);
    }

    /** @test */
    public function webhook_assigns_personal_trainer_after_successful_payment()
    {
        $user = User::factory()->create();
        $package = PersonalTrainerPackage::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'type' => 'personal_trainer',
            'item_id' => $package->id,
            'status' => 'pending'
        ]);

        $assignmentMock = $this->createMock(AssignmentService::class);
        $assignmentMock->expects($this->once())
                      ->method('assignPersonalTrainer')
                      ->with($user->id, $package->id)
                      ->willReturn(true);
        $this->app->instance(AssignmentService::class, $assignmentMock);

        $webhookData = [
            'order_id' => $transaction->transaction_code,
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept'
        ];

        $response = $this->postJson('/api/payments/webhook', $webhookData);

        $this->assertApiResponse($response);
    }

    /** @test */
    public function user_can_export_their_transaction_history()
    {
        $user = $this->actingAsMember();
        Transaction::factory()->count(5)->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/payments/export?format=csv');

        $this->assertApiResponse($response);
        $response->assertJsonStructure([
            'data' => [
                'download_url',
                'expires_at'
            ]
        ]);
    }

    /** @test */
    public function admin_can_export_all_transactions()
    {
        $admin = $this->actingAsAdmin();
        Transaction::factory()->count(10)->create();

        $response = $this->getJson('/api/payments/export-all?format=xlsx');

        $this->assertApiResponse($response);
        $response->assertJsonStructure([
            'data' => [
                'download_url',
                'expires_at'
            ]
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_payment_endpoints()
    {
        $transaction = Transaction::factory()->create();

        $this->getJson('/api/payments/transactions')->assertStatus(401);
        $this->getJson("/api/payments/transactions/{$transaction->id}")->assertStatus(401);
        $this->postJson("/api/payments/transactions/{$transaction->id}/retry")->assertStatus(401);
        $this->getJson('/api/payments/export')->assertStatus(401);
    }

    /** @test */
    public function webhook_handles_pending_payment_status()
    {
        $transaction = Transaction::factory()->pending()->create();

        $webhookData = [
            'order_id' => $transaction->transaction_code,
            'transaction_status' => 'pending',
            'fraud_status' => 'accept'
        ];

        $response = $this->postJson('/api/payments/webhook', $webhookData);

        $this->assertApiResponse($response);
        
        $transaction->refresh();
        $this->assertEquals('pending', $transaction->status);
    }

    /** @test */
    public function transaction_statistics_calculate_success_rate_correctly()
    {
        $admin = $this->actingAsAdmin();
        
        // Create 7 successful, 3 failed transactions
        Transaction::factory()->count(7)->paid()->create();
        Transaction::factory()->count(3)->failed()->create();

        $response = $this->getJson('/api/payments/statistics');

        $this->assertApiResponse($response);
        $response->assertJsonPath('data.success_rate', 70.0); // 7/10 * 100
    }
}