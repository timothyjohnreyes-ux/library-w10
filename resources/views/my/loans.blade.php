<x-app-layout>
    <x-slot name="header"><div><p class="text-sm font-semibold uppercase tracking-wider text-blue-600">Member area</p><h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">{{ __('messages.my_loans') }}</h1><p class="mt-1 text-sm text-slate-500">Your complete borrowing history.</p></div></x-slot>
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto"><table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50"><tr class="text-left text-xs font-semibold uppercase tracking-wider text-slate-500"><th class="px-6 py-4">Book</th><th class="px-6 py-4">Author</th><th class="px-6 py-4">Borrowed</th><th class="px-6 py-4">Returned</th><th class="px-6 py-4">Status</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($books as $book)
                    <tr><td class="px-6 py-4 font-semibold text-slate-900">{{ $book->title }}</td><td class="px-6 py-4 text-sm text-slate-600">{{ $book->author->name }}</td><td class="px-6 py-4 text-sm text-slate-600">{{ \Illuminate\Support\Carbon::parse($book->pivot->borrowed_at)->format('M j, Y') }}</td><td class="px-6 py-4 text-sm text-slate-600">{{ $book->pivot->returned_at ? \Illuminate\Support\Carbon::parse($book->pivot->returned_at)->format('M j, Y') : '—' }}</td><td class="px-6 py-4">@if ($book->pivot->returned_at)<span class="badge badge-blue">Returned</span>@else<span class="badge badge-amber">Active</span>@endif</td></tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-14 text-center"><p class="font-semibold text-slate-900">{{ __('messages.no_loans') }}</p><p class="mt-1 text-sm text-slate-500">Browse the catalogue to find your next book.</p><a href="{{ route('books.index') }}" class="mt-4 inline-flex rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white">{{ __('messages.books') }}</a></td></tr>
                @endforelse
            </tbody>
        </table></div>
        <div class="border-t border-slate-200 px-6 py-4">{{ $books->links() }}</div>
    </div>
</x-app-layout>
