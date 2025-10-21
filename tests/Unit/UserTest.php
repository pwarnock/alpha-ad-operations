<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class UserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_user_can_be_created()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertNotNull($user->password);
        $this->assertNotNull($user->remember_token);
    }

    #[Test]
    public function a_user_has_api_tokens()
    {
        $user = User::factory()->create();
        
        $token = $user->createToken('test-token');
        
        $this->assertNotNull($token);
        $this->assertEquals('test-token', $token->accessToken->name);
        $this->assertTrue($user->tokens()->exists());
    }

    #[Test]
    public function a_user_can_have_multiple_tokens()
    {
        $user = User::factory()->create();
        
        $token1 = $user->createToken('token1');
        $token2 = $user->createToken('token2');
        
        $this->assertEquals(2, $user->tokens()->count());
        $this->assertEquals('token1', $token1->accessToken->name);
        $this->assertEquals('token2', $token2->accessToken->name);
    }

    #[Test]
    public function a_user_can_revoke_tokens()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token');
        
        $user->tokens()->delete();
        
        $this->assertEquals(0, $user->tokens()->count());
    }

    #[Test]
    public function user_password_is_hashed()
    {
        $user = User::factory()->create(['password' => 'plaintext']);
        
        $this->assertNotEquals('plaintext', $user->password);
        $this->assertTrue(\Hash::check('plaintext', $user->password));
    }

    #[Test]
    public function user_email_is_unique()
    {
        User::factory()->create(['email' => 'test@example.com']);
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        User::factory()->create(['email' => 'test@example.com']);
    }

    #[Test]
    public function user_can_check_password()
    {
        $user = User::factory()->create(['password' => 'secret123']);
        
        $this->assertTrue(\Hash::check('secret123', $user->password));
        $this->assertFalse(\Hash::check('wrongpassword', $user->password));
    }

    #[Test]
    public function user_fillable_attributes()
    {
        $user = new User();
        
        $expectedFillable = ['name', 'email', 'password', 'organization_id', 'organizational_unit_id', 'tenant_id', 'is_admin'];
        $this->assertEquals($expectedFillable, $user->getFillable());
    }

    #[Test]
    public function user_hidden_attributes()
    {
        $user = User::factory()->create();
        $array = $user->toArray();
        
        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('email', $array);
    }

    #[Test]
    public function user_casts()
    {
        $user = User::factory()->create([
            'email_verified_at' => '2023-01-01 12:00:00',
        ]);
        
        $this->assertInstanceOf(\Carbon\Carbon::class, $user->email_verified_at);
        $this->assertEquals('2023-01-01 12:00:00', $user->email_verified_at->format('Y-m-d H:i:s'));
    }
}