<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LibraryWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_browse_books_but_cannot_borrow(): void
    {
        $book = $this->makeBook();

        $this->get(route('books.index'))->assertOk();
        $this->get(route('books.show', $book))->assertOk();
        $this->post(route('books.borrow', $book))->assertRedirect(route('login'));
    }

    public function test_member_can_borrow_normal_book_and_cannot_borrow_reference_or_duplicate_active_loan(): void
    {
        $memberUser = User::factory()->create(['role' => 'member']);
        $member = Member::create([
            'user_id' => $memberUser->id,
            'name' => $memberUser->name,
            'email' => 'member-'.$memberUser->id.'@example.com',
        ]);
        $book = $this->makeBook();
        $referenceBook = $this->makeBook(['is_reference' => true]);

        $this->actingAs($memberUser)
            ->post(route('books.borrow', $book))
            ->assertRedirect(route('books.show', $book));

        $this->assertDatabaseHas('book_member', [
            'member_id' => $member->id,
            'book_id' => $book->id,
            'returned_at' => null,
        ]);

        $this->actingAs($memberUser)
            ->post(route('books.borrow', $book))
            ->assertSessionHasErrors('book');

        $this->actingAs($memberUser)
            ->post(route('books.borrow', $referenceBook))
            ->assertSessionHasErrors('book');
    }

    public function test_returned_book_can_be_borrowed_again_and_history_is_preserved(): void
    {
        $memberUser = User::factory()->create(['role' => 'member']);
        $member = Member::create([
            'user_id' => $memberUser->id,
            'name' => $memberUser->name,
            'email' => 'member-'.$memberUser->id.'@example.com',
        ]);
        $book = $this->makeBook();
        $member->books()->attach($book->id, [
            'borrowed_at' => now()->subDay(),
            'returned_at' => now(),
        ]);

        $this->actingAs($memberUser)
            ->post(route('books.borrow', $book))
            ->assertRedirect(route('books.show', $book));

        $this->assertDatabaseCount('book_member', 2);
        $this->assertDatabaseHas('book_member', [
            'member_id' => $member->id,
            'book_id' => $book->id,
            'returned_at' => null,
        ]);
    }

    public function test_an_active_book_cannot_be_borrowed_by_another_member_and_is_shown_as_active_to_its_borrower(): void
    {
        $firstUser = User::factory()->create(['role' => 'member']);
        $firstMember = Member::create([
            'user_id' => $firstUser->id,
            'name' => $firstUser->name,
            'email' => 'first-'.$firstUser->id.'@example.com',
        ]);
        $secondUser = User::factory()->create(['role' => 'member']);
        $secondMember = Member::create([
            'user_id' => $secondUser->id,
            'name' => $secondUser->name,
            'email' => 'second-'.$secondUser->id.'@example.com',
        ]);
        $book = $this->makeBook();

        $firstMember->books()->attach($book->id, [
            'borrowed_at' => now(),
            'returned_at' => null,
        ]);

        $this->actingAs($firstUser)
            ->get(route('books.index'))
            ->assertSee('Active');

        $this->actingAs($secondUser)
            ->post(route('books.borrow', $book))
            ->assertSessionHasErrors('book');

        $this->assertDatabaseMissing('book_member', [
            'member_id' => $secondMember->id,
            'book_id' => $book->id,
        ]);
    }

    public function test_only_librarians_can_return_loans_and_admins_can_access_the_desk(): void
    {
        $member = Member::create([
            'name' => 'Borrower',
            'email' => 'borrower@example.com',
        ]);
        $book = $this->makeBook();
        $member->books()->attach($book->id, [
            'borrowed_at' => now()->subDay(),
            'returned_at' => null,
        ]);
        $admin = User::factory()->create(['role' => 'admin']);
        $librarian = User::factory()->create(['role' => 'librarian']);

        $this->actingAs($admin)->get(route('desk'))->assertOk();
        $this->actingAs($admin)
            ->patch(route('loans.return', [$member, $book]))
            ->assertForbidden();

        $this->actingAs($librarian)
            ->patch(route('loans.return', [$member, $book]))
            ->assertRedirect();

        $this->assertNotNull(DB::table('book_member')
            ->where('member_id', $member->id)
            ->where('book_id', $book->id)
            ->value('returned_at'));
    }

    public function test_member_cannot_access_staff_pages_and_admin_cannot_change_own_role(): void
    {
        $member = User::factory()->create(['role' => 'member']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($member)->get(route('desk'))->assertForbidden();
        $this->actingAs($member)->get(route('admin.users'))->assertForbidden();
        $this->actingAs($admin)
            ->patch(route('admin.users.update-role', $admin), ['role' => 'member'])
            ->assertRedirect()
            ->assertSessionHas('error', 'You cannot change your own role.');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'role' => 'admin',
        ]);
    }

    public function test_member_registration_creates_a_linked_member_record(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Timothy Reader',
            'email' => 'timothy@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('books.index'));

        $user = User::where('email', 'timothy@example.com')->firstOrFail();

        $this->assertDatabaseHas('members', [
            'user_id' => $user->id,
            'name' => 'Timothy Reader',
            'email' => 'timothy@example.com',
        ]);

        $this->actingAs($user)->get(route('my.loans'))->assertOk();
    }

    public function test_librarian_desk_supports_status_filters_and_pagination(): void
    {
        $librarian = User::factory()->create(['role' => 'librarian']);
        $member = Member::create([
            'name' => 'Borrower',
            'email' => 'borrower@example.com',
        ]);
        $activeBook = $this->makeBook();
        $returnedBook = $this->makeBook();

        $member->books()->attach($activeBook->id, [
            'borrowed_at' => now()->subDay(),
            'returned_at' => null,
        ]);
        $member->books()->attach($returnedBook->id, [
            'borrowed_at' => now()->subDays(2),
            'returned_at' => now()->subDay(),
        ]);

        $this->actingAs($librarian)
            ->get(route('desk', ['status' => 'active']))
            ->assertOk()
            ->assertSee($activeBook->title)
            ->assertDontSee($returnedBook->title);

        $this->actingAs($librarian)
            ->get(route('desk', ['status' => 'returned']))
            ->assertOk()
            ->assertSee($returnedBook->title)
            ->assertDontSee($activeBook->title);
    }

    private function makeBook(array $attributes = []): Book
    {
        $author = Author::create(['name' => fake()->name()]);

        return Book::create(array_merge([
            'author_id' => $author->id,
            'isbn' => fake()->unique()->numerify('#############'),
            'title' => fake()->sentence(3),
            'published_year' => 2020,
            'is_reference' => false,
        ], $attributes));
    }
}
