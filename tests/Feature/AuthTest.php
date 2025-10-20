<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // #[Test]
    // public function a_user_can_register()
    // {
    //     $response = $this->post('/register', [
    //         'name' => 'Test User',
    //         'email' => 'test@example.com',
    //         'password' => 'password',
    //         'password_confirmation' => 'password',
    //     ]);

    //     $response->assertRedirect('/admin'); // Assuming successful registration redirects to /admin
    //     $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    // }

    #[Test]
    public function a_user_can_log_in()
    {
        $user = User::factory()->create([
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
        ]);

                $user = User::factory()->create([

                    'email' => 'test@example.com',

                    'password' => bcrypt('password'),

                ]);

        

                \Livewire\Livewire::test('Filament\\Http\\Livewire\\Auth\\Login')

                    ->set('data.email', 'test@example.com')

                    ->set('data.password', 'password')

                    ->call('authenticate');

        

                $this->assertAuthenticatedAs($user);
    }
}
