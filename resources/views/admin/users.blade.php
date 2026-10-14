<x-app-layout>
    <x-slot name="header"><div><p class="text-sm font-semibold uppercase tracking-wider text-blue-600">Administration</p><h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">User management</h1><p class="mt-1 text-sm text-slate-500">Manage access levels for library staff and members.</p></div></x-slot>
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto"><table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50"><tr class="text-left text-xs font-semibold uppercase tracking-wider text-slate-500"><th class="px-6 py-4">User</th><th class="px-6 py-4">Current role</th><th class="px-6 py-4 text-right">Update role</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($users as $user)
                    <tr class="hover:bg-slate-50"><td class="px-6 py-5"><p class="font-semibold text-slate-900">{{ $user->name }}</p><p class="mt-1 text-sm text-slate-500">{{ $user->email }}</p></td><td class="px-6 py-5"><span class="badge {{ $user->role === 'admin' ? 'badge-blue' : ($user->role === 'librarian' ? 'badge-emerald' : 'badge-amber') }}">{{ ucfirst($user->role) }}</span></td><td class="px-6 py-5 text-right">
                        @if (auth()->id() === $user->id)
                            <span class="text-sm text-slate-400">Your account</span>
                        @else
                            <form action="{{ route('admin.users.update-role', $user) }}" method="POST" class="flex justify-end gap-2">@csrf @method('PATCH')<label for="role-{{ $user->id }}" class="sr-only">Role for {{ $user->name }}</label><select id="role-{{ $user->id }}" name="role" class="rounded-lg border-slate-300 py-2 text-sm focus:border-blue-500 focus:ring-blue-500"><option value="member" @selected($user->role === 'member')>Member</option><option value="librarian" @selected($user->role === 'librarian')>Librarian</option><option value="admin" @selected($user->role === 'admin')>Admin</option></select><button class="rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800">Save</button></form>
                        @endif
                    </td></tr>
                @endforeach
            </tbody>
        </table></div>
        <div class="border-t border-slate-200 px-6 py-4">{{ $users->links() }}</div>
    </div>
</x-app-layout>
