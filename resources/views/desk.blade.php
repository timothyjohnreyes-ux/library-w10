<x-app-layout>
    <x-slot name="header"><div><p class="text-sm font-semibold uppercase tracking-wider text-blue-600">Operations</p><h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Librarian desk</h1><p class="mt-1 text-sm text-slate-500">Monitor active and historical loans across the library.</p></div></x-slot>
    <div class="space-y-5">
        <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-end sm:justify-between">
            <div><p class="text-sm text-slate-500">Matching loan records</p><p class="mt-1 text-3xl font-bold text-slate-900">{{ $loanCount }}</p></div>
            <form method="GET" action="{{ route('desk') }}" class="flex items-end gap-3">
                <div><label for="status" class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Filter status</label><select id="status" name="status" onchange="this.form.submit()" class="mt-2 rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"><option value="all" @selected($status === 'all')>All loans</option><option value="active" @selected($status === 'active')>Active only</option><option value="returned" @selected($status === 'returned')>Returned only</option></select></div>
            </form>
        </div>
        @forelse ($loans as $loan)
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex items-start gap-4">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 font-bold text-blue-700">{{ strtoupper(substr($loan->book_title, 0, 1)) }}</div>
                            <div><h2 class="font-semibold text-slate-900">{{ $loan->book_title }}</h2><p class="mt-1 text-sm text-slate-600">Borrower: {{ $loan->member_name }}</p><p class="mt-1 text-xs text-slate-500">{{ $loan->author_name }} · Borrowed {{ \Illuminate\Support\Carbon::parse($loan->borrowed_at)->format('M j, Y') }}</p></div>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            @if ($loan->returned_at)
                                <span class="badge badge-blue">Returned {{ \Illuminate\Support\Carbon::parse($loan->returned_at)->format('M j, Y') }}</span>
                            @else
                                <span class="badge badge-amber">Active loan</span>
                                @if (auth()->user()->role === 'librarian')
                                    <form action="{{ route('loans.return', [$loan->member_id, $loan->book_id]) }}" method="POST" onsubmit="return confirm('Mark this loan as returned?')">@csrf @method('PATCH')<button class="rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800">Mark returned</button></form>
                                @endif
                            @endif
                        </div>
                    </div>
                </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center"><p class="font-semibold text-slate-900">No loan records found</p><p class="mt-1 text-sm text-slate-500">Loan activity will appear here once books are borrowed.</p></div>
        @endforelse
        <div class="rounded-2xl border border-slate-200 bg-white px-6 py-4 shadow-sm">{{ $loans->links() }}</div>
    </div>
</x-app-layout>
