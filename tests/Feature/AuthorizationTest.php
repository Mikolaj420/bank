<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_client_cannot_access_admin_area(): void
    {
        $client = User::factory()->create();

        $this->actingAs($client)->get('/admin/users')->assertForbidden();
    }

    public function test_client_cannot_access_manager_approvals(): void
    {
        $client = User::factory()->create();

        $this->actingAs($client)->get('/approvals')->assertForbidden();
    }

    public function test_employee_cannot_access_admin_area(): void
    {
        $employee = User::factory()->role(Role::EMPLOYEE)->create();

        $this->actingAs($employee)->get('/admin/users')->assertForbidden();
    }

    public function test_admin_can_access_admin_area(): void
    {
        $admin = User::factory()->role(Role::ADMIN)->create();

        $this->actingAs($admin)->get('/admin/users')->assertOk();
    }
}
