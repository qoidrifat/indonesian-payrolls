<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticatedRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_requires_auth(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_authenticated_dashboard_loads(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/dashboard')->assertOk();
    }

    public function test_employees_list_loads(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/employees')->assertOk();
    }

    public function test_payrolls_list_loads(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/payrolls')->assertOk();
    }
}
