<?php

namespace Tests\Feature\Api;

use App\Models\KycRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KycApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Fake the public storage disk
        Storage::fake('public');
    }

    public function test_unauthenticated_users_cannot_access_kyc_endpoints(): void
    {
        $this->getJson('/api/kyc/status')->assertStatus(401);
        $this->postJson('/api/kyc/submit', [])->assertStatus(401);
        $this->getJson('/api/kyc/history')->assertStatus(401);
    }

    public function test_authenticated_user_can_get_status_with_no_requests(): void
    {
        $user = User::factory()->create([
            'status' => 'pending',
            'kyc_level' => 0,
            'identity_verified_at' => null,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/kyc/status');

        $response->assertStatus(200)
            ->assertJson([
                'error' => false,
                'data' => [
                    'kyc_status' => 'not_submitted',
                    'kyc_level' => 0,
                    'identity_verified_at' => null,
                    'user_status' => 'pending',
                    'latest_request' => null,
                ]
            ]);
    }

    public function test_user_can_submit_kyc_request(): void
    {
        $user = User::factory()->create([
            'status' => 'pending',
        ]);

        Sanctum::actingAs($user);

        $idImage = UploadedFile::fake()->image('national_id.jpg');
        $selfieImage = UploadedFile::fake()->image('selfie.jpg');

        $response = $this->postJson('/api/kyc/submit', [
            'full_name' => 'Mohamed Ali',
            'country' => 'Saudi Arabia',
            'id_number' => '1020304050',
            'id_image' => $idImage,
            'selfie_image' => $selfieImage,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'error' => false,
                'data' => [
                    'full_name' => 'Mohamed Ali',
                    'country' => 'Saudi Arabia',
                    'id_number' => '1020304050',
                    'status' => 'pending',
                ]
            ]);

        $this->assertDatabaseHas('kyc_requests', [
            'user_id' => $user->id,
            'full_name' => 'Mohamed Ali',
            'id_number' => '1020304050',
            'status' => 'pending',
        ]);

        $kycRequest = KycRequest::first();
        Storage::disk('public')->assertExists($kycRequest->id_image);
        Storage::disk('public')->assertExists($kycRequest->selfie_image);
    }

    public function test_user_cannot_submit_duplicate_pending_kyc_request(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Create an existing pending request
        KycRequest::create([
            'user_id' => $user->id,
            'full_name' => 'Mohamed Ali',
            'country' => 'Saudi Arabia',
            'id_number' => '1020304050',
            'id_image' => 'kyc/id.jpg',
            'selfie_image' => 'kyc/selfie.jpg',
            'status' => 'pending',
        ]);

        $idImage = UploadedFile::fake()->image('national_id.jpg');
        $selfieImage = UploadedFile::fake()->image('selfie.jpg');

        $response = $this->postJson('/api/kyc/submit', [
            'full_name' => 'Mohamed Ali',
            'country' => 'Saudi Arabia',
            'id_number' => '1020304050',
            'id_image' => $idImage,
            'selfie_image' => $selfieImage,
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'error' => true,
                'message' => 'You already have a pending verification request.',
            ]);
    }

    public function test_user_cannot_submit_kyc_if_already_verified(): void
    {
        $user = User::factory()->create([
            'status' => 'approved',
            'kyc_level' => 3,
        ]);
        Sanctum::actingAs($user);

        $idImage = UploadedFile::fake()->image('national_id.jpg');
        $selfieImage = UploadedFile::fake()->image('selfie.jpg');

        $response = $this->postJson('/api/kyc/submit', [
            'full_name' => 'Mohamed Ali',
            'country' => 'Saudi Arabia',
            'id_number' => '1020304050',
            'id_image' => $idImage,
            'selfie_image' => $selfieImage,
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'error' => true,
                'message' => 'Your identity is already verified.',
            ]);
    }

    public function test_user_can_view_history(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        KycRequest::create([
            'user_id' => $user->id,
            'full_name' => 'Mohamed Ali',
            'country' => 'Saudi Arabia',
            'id_number' => '1020304050',
            'id_image' => 'kyc/id1.jpg',
            'selfie_image' => 'kyc/selfie1.jpg',
            'status' => 'rejected',
            'admin_note' => 'Blurry image',
        ]);

        KycRequest::create([
            'user_id' => $user->id,
            'full_name' => 'Mohamed Ali',
            'country' => 'Saudi Arabia',
            'id_number' => '1020304050',
            'id_image' => 'kyc/id2.jpg',
            'selfie_image' => 'kyc/selfie2.jpg',
            'status' => 'pending',
        ]);

        $response = $this->getJson('/api/kyc/history');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data.requests');
    }
}
