<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\RedirectResponse;

class RoleRedirect
{
    /**
     * @param  array<string, scalar>  $query
     */
    public static function to(User $user, array $query = []): RedirectResponse
    {
        return match ($user->role) {
            'admin' => redirect()->route('dashboard', $query),
            'librarian' => redirect()->route('desk', $query),
            default => redirect()->route('books.index', $query),
        };
    }
}
