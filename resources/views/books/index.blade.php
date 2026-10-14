<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-blue-600">Catalogue</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">{{ __('messages.books') }}</h1>
                <p class="mt-1 text-sm text-slate-500">Discover books by title, author, or ISBN.</p>
            </div>
            @auth
                @if (in_array(auth()->user()->role, ['librarian', 'admin'], true))
                    <a href="{{ route('books.create') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Add book</a>
                @endif
            @endauth
        </div>
    </x-slot>

    <form method="GET" action="{{ route('books.index') }}" class="mb-6 flex flex-col gap-3 sm:flex-row">
        <label for="search" class="sr-only">Search books</label>
        <input id="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Search title, author, or ISBN') }}" class="w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <button class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800">{{ __('messages.search') }}</button>
        @if (request('search'))
            <a href="{{ route('books.index') }}" class="rounded-xl border border-slate-300 bg-white px-5 py-3 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">{{ __('messages.clear') }}</a>
        @endif
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="hidden overflow-x-auto md:block">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <th class="px-6 py-4">Book</th><th class="px-6 py-4">Author</th><th class="px-6 py-4">ISBN</th><th class="px-6 py-4">Year</th><th class="px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($books as $book)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4"><a href="{{ route('books.show', $book) }}" class="font-semibold text-slate-900 hover:text-blue-600">{{ $book->title }}</a></td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $book->author->name }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $book->isbn }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $book->published_year }}</td>
                            <td class="px-6 py-4">@if ($book->is_reference)<span class="badge badge-amber">Reference</span>@elseif (auth()->check() && auth()->user()->role === 'member' && (int) $book->active_loan_member_id === (int) auth()->user()->member?->id)<span class="badge badge-blue">Active</span>@elseif ($book->active_loan_member_id)<span class="badge badge-amber">Borrowed</span>@else<span class="badge badge-emerald">Available</span>@endif</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500">No books matched your search.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="grid gap-4 p-4 md:hidden">
            @forelse ($books as $book)
                <a href="{{ route('books.show', $book) }}" class="rounded-xl border border-slate-200 p-4 hover:border-blue-300">
                    <div class="flex items-start justify-between gap-3"><h2 class="font-semibold text-slate-900">{{ $book->title }}</h2>@if ($book->is_reference)<span class="badge badge-amber">Reference</span>@elseif (auth()->check() && auth()->user()->role === 'member' && (int) $book->active_loan_member_id === (int) auth()->user()->member?->id)<span class="badge badge-blue">Active</span>@elseif ($book->active_loan_member_id)<span class="badge badge-amber">Borrowed</span>@else<span class="badge badge-emerald">Available</span>@endif</div>
                    <p class="mt-2 text-sm text-slate-600">{{ $book->author->name }}</p><p class="mt-1 text-xs text-slate-500">{{ $book->isbn }} · {{ $book->published_year }}</p>
                </a>
            @empty
                <p class="py-8 text-center text-sm text-slate-500">No books matched your search.</p>
            @endforelse
        </div>
        <div class="border-t border-slate-200 px-6 py-4">{{ $books->links() }}</div>
    </div>
</x-app-layout>
