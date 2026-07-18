<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginRedirectTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_is_sent_to_admin_panel_after_login(): void
    {
        $admin = User::where('email', 'owner@auraquina.id')->firstOrFail();

        $this->post(route('login.attempt'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect('/admin');
    }

    public function test_authenticated_admin_opening_login_is_sent_to_admin_panel(): void
    {
        $admin = User::where('email', 'owner@auraquina.id')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('login'))
            ->assertRedirect('/admin');
    }

    public function test_regular_user_is_sent_to_store_after_login(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $this->post(route('login.attempt'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect('/');
    }
}
