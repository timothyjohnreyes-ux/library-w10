<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_another_users_role(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $user = User::factory()->create([
            'role' => 'member',
        ]);

        $response = $this->actingAs($admin)->patch(
            route('admin.users.update-role', $user),
            ['role' => 'librarian'],
        );

        $response->assertRedirect();
        $response->assertSessionHas('success', 'User role updated successfully.');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'librarian',
        ]);
    }
}
