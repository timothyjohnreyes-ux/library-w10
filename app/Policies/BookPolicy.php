<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;

class BookPolicy
{
    /**
     * Admins can perform all abilities.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return null;
    }

    /**
     * Anyone can view the list of books.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Anyone can view a book.
     */
    public function view(?User $user, Book $book): bool
    {
        return true;
    }

    /**
     * Only librarians can create books.
     */
    public function create(User $user): bool
    {
        return $user->role === 'librarian';
    }

    /**
     * Only librarians can update books.
     */
    public function update(User $user, Book $book): bool
    {
        return $user->role === 'librarian';
    }

    /**
     * Only librarians can delete books.
     */
    public function delete(User $user, Book $book): bool
    {
        return $user->role === 'librarian';
    }
}
