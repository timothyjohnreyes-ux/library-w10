<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_access_member_only_route(): void
    {
        Route::get('/test-member-route', function () {
            return 'ok';
        })->middleware('role:member');

        $user = User::factory()->create([
            'role' => 'member',
        ]);

        $response = $this->actingAs($user)->get('/test-member-route');

        $response->assertOk();
        $response->assertSeeText('ok');
    }

    public function test_admin_can_access_librarian_only_route(): void
    {
        Route::get('/test-admin-route', function () {
            return 'ok';
        })->middleware('role:librarian');

        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)->get('/test-admin-route');

        $response->assertOk();
        $response->assertSeeText('ok');
    }

    public function test_non_matching_role_is_forbidden(): void
    {
        Route::get('/test-forbidden-route', function () {
            return 'ok';
        })->middleware('role:librarian');

        $user = User::factory()->create([
            'role' => 'member',
        ]);

        $response = $this->actingAs($user)->get('/test-forbidden-route');

        $response->assertForbidden();
    }
}
