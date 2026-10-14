<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\User;

class LoanPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === 'admin' && $ability !== 'markReturned') {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['member', 'librarian']);
    }

    public function view(User $user, Member $member): bool
    {
        if ($user->role === 'librarian') {
            return true;
        }

        return $user->role === 'member'
            && $user->member?->is($member);
    }

    public function markReturned(User $user, Member $member): bool
    {
        return $user->role === 'librarian';
    }
}
