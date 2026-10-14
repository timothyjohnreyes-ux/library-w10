<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div><p class="text-sm font-semibold uppercase tracking-wider text-blue-600">Book details</p><h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">{{ $book->title }}</h1></div>
            <a href="{{ route('books.index') }}" class="text-sm font-semibold text-slate-600 hover:text-blue-600">← Back to books</a>
        </div>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-[1fr_320px]">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="flex items-start justify-between gap-6">
                <div><p class="text-sm font-medium text-slate-500">Catalogue record</p><h2 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">{{ $book->title }}</h2><p class="mt-2 text-lg text-slate-600">by {{ $book->author->name }}</p></div>
                @if ($book->is_reference)<span class="badge badge-amber">Reference book</span>@else<span class="badge badge-emerald">Borrowable</span>@endif
            </div>
            <dl class="mt-10 grid gap-5 border-t border-slate-100 pt-6 sm:grid-cols-3">
                <div><dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">ISBN</dt><dd class="mt-1 text-sm font-medium text-slate-900">{{ $book->isbn }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Published</dt><dd class="mt-1 text-sm font-medium text-slate-900">{{ $book->published_year }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Author</dt><dd class="mt-1 text-sm font-medium text-slate-900">{{ $book->author->name }}</dd></div>
            </dl>
        </section>

        <aside class="rounded-2xl bg-slate-950 p-6 text-white shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-wider text-blue-300">Actions</p>
            @if ($book->is_reference)
                <p class="mt-4 text-sm leading-6 text-slate-300">This reference book is available for reading in the library and cannot be borrowed.</p>
            @elseif ($activeLoan && (int) $activeLoan->member_id === (int) $currentMemberId)
                <p class="mt-4 text-sm leading-6 text-slate-300">You currently have this book borrowed.</p>
                <span class="mt-6 block rounded-lg bg-blue-500/20 px-4 py-3 text-center text-sm font-semibold text-blue-200">Active loan</span>
            @elseif ($activeLoan)
                <p class="mt-4 text-sm leading-6 text-slate-300">This book is currently borrowed by another member.</p>
                <span class="mt-6 block rounded-lg bg-amber-500/20 px-4 py-3 text-center text-sm font-semibold text-amber-200">Currently borrowed</span>
            @elseif (auth()->check() && auth()->user()->role === 'member')
                <p class="mt-4 text-sm leading-6 text-slate-300">Borrow this book using your member account.</p>
                <form action="{{ route('books.borrow', $book) }}" method="POST" class="mt-6">@csrf<button class="w-full rounded-lg bg-blue-500 px-4 py-3 text-sm font-semibold hover:bg-blue-400">Borrow book</button></form>
            @elseif (! auth()->check())
                <p class="mt-4 text-sm leading-6 text-slate-300">Sign in with a member account to borrow this book.</p>
                <a href="{{ route('login') }}" class="mt-6 block rounded-lg bg-blue-500 px-4 py-3 text-center text-sm font-semibold hover:bg-blue-400">Sign in</a>
            @else
                <p class="mt-4 text-sm leading-6 text-slate-300">Only member accounts can borrow books.</p>
            @endif

            @auth
                @if (in_array(auth()->user()->role, ['librarian', 'admin'], true))
                    <div class="mt-6 border-t border-white/10 pt-6"><a href="{{ route('books.edit', $book) }}" class="block rounded-lg border border-white/20 px-4 py-3 text-center text-sm font-semibold text-white hover:bg-white/10">Edit book</a>
                    <form action="{{ route('books.destroy', $book) }}" method="POST" class="mt-3" onsubmit="return confirm('Delete this book?')">@csrf @method('DELETE')<button class="w-full rounded-lg px-4 py-2 text-sm text-rose-300 hover:bg-rose-500/10">Delete book</button></form></div>
                @endif
            @endauth
        </aside>
    </div>
</x-app-layout>
