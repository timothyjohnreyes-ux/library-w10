<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index()
    {
        Gate::authorize('manage-users');

        $users = User::latest()->paginate(10);

        return view('admin.users', compact('users'));
    }

    public function updateRole(Request $request, User $user)
    {
        Gate::authorize('manage-users');

        if ($request->user()->is($user)) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $validated = $request->validate([
            'role' => ['required', Rule::in(['member', 'librarian', 'admin'])],
        ]);

        $user->update([
            'role' => $validated['role'],
        ]);

        return back()->with('success', 'User role updated successfully.');
    }
}
