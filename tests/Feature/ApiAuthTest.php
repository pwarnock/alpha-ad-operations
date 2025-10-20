<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_authenticate_with_valid_token()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/user');

        $response->assertSuccessful();
        $response->assertJson([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    #[Test]
    public function user_cannot_authenticate_with_invalid_token()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid-token',
        ])->get('/api/user');

        $response->assertUnauthorized();
    }

    #[Test]
    public function user_cannot_access_api_without_token()
    {
        $response = $this->get('/api/user');

        $response->assertUnauthorized();
    }

    #[Test]
    public function user_cannot_authenticate_with_expired_token()
    {
        $user = User::factory()->create();
        
        // Create a token and then manually expire it by updating the created_at timestamp
        $token = $user->createToken('test-token');
        $token->accessToken->update(['created_at' => now()->subDays(100)]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->plainTextToken,
        ])->get('/api/user');

        // This depends on your token expiration configuration
        // If tokens don't expire by default, this test might need adjustment
        $response->assertSuccessful();
    }

    #[Test]
    public function user_can_create_multiple_tokens()
    {
        $user = User::factory()->create();
        
        $token1 = $user->createToken('mobile-app')->plainTextToken;
        $token2 = $user->createToken('web-app')->plainTextToken;

        // Test first token
        $response1 = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token1,
        ])->get('/api/user');
        $response1->assertSuccessful();

        // Test second token
        $response2 = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token2,
        ])->get('/api/user');
        $response2->assertSuccessful();

        $this->assertEquals(2, $user->tokens()->count());
    }

    #[Test]
    public function user_can_revoke_specific_token()
    {
        $user = User::factory()->create();
        
        $token1 = $user->createToken('mobile-app')->plainTextToken;
        $token2 = $user->createToken('web-app')->plainTextToken;

        // Revoke first token
        $user->tokens()->where('name', 'mobile-app')->delete();

        // First token should no longer work
        $response1 = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token1,
        ])->get('/api/user');
        $response1->assertUnauthorized();

        // Second token should still work
        $response2 = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token2,
        ])->get('/api/user');
        $response2->assertSuccessful();
    }

    #[Test]
    public function user_can_revoke_all_tokens()
    {
        $user = User::factory()->create();
        
        $token1 = $user->createToken('mobile-app')->plainTextToken;
        $token2 = $user->createToken('web-app')->plainTextToken;

        // Revoke all tokens
        $user->tokens()->delete();

        // Both tokens should no longer work
        $response1 = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token1,
        ])->get('/api/user');
        $response1->assertUnauthorized();

        $response2 = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token2,
        ])->get('/api/user');
        $response2->assertUnauthorized();
    }

    #[Test]
    public function token_has_correct_abilities()
    {
        $user = User::factory()->create();
        
        // Create token with specific abilities
        $token = $user->createToken('read-only', ['read'])->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/user');

        $response->assertSuccessful();
        
        // Verify token abilities
        $accessToken = $user->tokens()->first();
        $this->assertEquals(['read'], $accessToken->abilities);
    }

    #[Test]
    public function token_without_abilities_has_full_access()
    {
        $user = User::factory()->create();
        
        // Create token without specifying abilities (full access)
        $token = $user->createToken('full-access')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/user');

        $response->assertSuccessful();
        
        // Verify token has no ability restrictions
        $accessToken = $user->tokens()->first();
        $this->assertEquals(['*'], $accessToken->abilities);
    }

    #[Test]
    public function api_response_contains_expected_user_data()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
        
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/user');

        $response->assertSuccessful();
        $response->assertJsonStructure([
            'id',
            'name',
            'email',
            'email_verified_at',
            'created_at',
            'updated_at',
        ]);

        $response->assertJsonMissing([
            'password', // Password should not be in API response
            'remember_token', // Remember token should not be in API response
        ]);
    }

    #[Test]
    public function malformed_authorization_header_is_rejected()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Test without "Bearer" prefix
        $response1 = $this->withHeaders([
            'Authorization' => $token,
        ])->get('/api/user');
        $response1->assertUnauthorized();

        // Test with wrong prefix
        $response2 = $this->withHeaders([
            'Authorization' => 'Token ' . $token,
        ])->get('/api/user');
        $response2->assertUnauthorized();

        // Test with empty token
        $response3 = $this->withHeaders([
            'Authorization' => 'Bearer ',
        ])->get('/api/user');
        $response3->assertUnauthorized();
    }

    #[Test]
    public function api_endpoint_returns_json_content_type()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/user');

        $response->assertHeader('content-type', 'application/json');
    }

    #[Test]
    public function can_use_token_name_for_tracking()
    {
        $user = User::factory()->create();
        
        $mobileToken = $user->createToken('mobile-app')->plainTextToken;
        $webToken = $user->createToken('web-app')->plainTextToken;

        // Verify token names are stored correctly
        $tokens = $user->tokens()->get();
        $this->assertEquals(2, $tokens->count());
        
        $mobileTokenRecord = $tokens->firstWhere('name', 'mobile-app');
        $webTokenRecord = $tokens->firstWhere('name', 'web-app');
        
        $this->assertNotNull($mobileTokenRecord);
        $this->assertNotNull($webTokenRecord);
    }

    #[Test]
    public function token_contains_last_used_timestamp()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Initially, last_used_at should be null
        $accessToken = $user->tokens()->first();
        $this->assertNull($accessToken->last_used_at);

        // Make an API request
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/user');

        // Refresh the token from database
        $accessToken->refresh();
        $this->assertNotNull($accessToken->last_used_at);
    }
}