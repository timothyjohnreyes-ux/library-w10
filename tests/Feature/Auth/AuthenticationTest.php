<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_librarians_are_redirected_to_the_desk_after_login(): void
    {
        $user = User::factory()->create(['role' => 'librarian']);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('desk', absolute: false));
    }

    public function test_members_are_redirected_to_books_after_login(): void
    {
        $user = User::factory()->create(['role' => 'member']);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('books.index', absolute: false));
    }

    public function test_only_admins_can_access_the_dashboard(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'member']))
            ->get(route('dashboard'))
            ->assertForbidden();

        $this->actingAs(User::factory()->unverified()->create(['role' => 'member']))
            ->get(route('dashboard'))
            ->assertForbidden();

        $this->actingAs(User::factory()->create(['role' => 'librarian']))
            ->get(route('dashboard'))
            ->assertForbidden();

        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_dashboard_navigation_is_hidden_for_non_admins_and_guests(): void
    {
        $this->get(route('books.index'))
            ->assertDontSee(route('dashboard'));

        $this->actingAs(User::factory()->create(['role' => 'member']))
            ->get(route('books.index'))
            ->assertDontSee(route('dashboard'));

        $this->actingAs(User::factory()->create(['role' => 'librarian']))
            ->get(route('books.index'))
            ->assertDontSee(route('dashboard'));

        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('books.index'))
            ->assertSee(route('dashboard'));
    }

    public function test_guests_are_redirected_to_login_from_the_dashboard(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
