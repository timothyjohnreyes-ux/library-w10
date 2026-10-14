<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-blue-600">{{ __('messages.dashboard') }}</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">{{ __('Welcome') }}, {{ auth()->user()->name }}</h1>
            </div>
            <p class="text-sm text-slate-500">A quick view of your library operations.</p>
        </div>
    </x-slot>

    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            ['label' => 'Total books', 'value' => $stats['books'], 'icon' => 'B', 'color' => 'blue'],
            ['label' => 'Members', 'value' => $stats['members'], 'icon' => 'M', 'color' => 'violet'],
            ['label' => 'Active loans', 'value' => $stats['active_loans'], 'icon' => 'A', 'color' => 'amber'],
            ['label' => 'Returned loans', 'value' => $stats['returned_loans'], 'icon' => 'R', 'color' => 'emerald'],
        ] as $stat)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-500">{{ $stat['label'] }}</p>
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-{{ $stat['color'] }}-50 text-sm font-bold text-{{ $stat['color'] }}-700">{{ $stat['icon'] }}</span>
                </div>
                <p class="mt-4 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($stat['value']) }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl bg-slate-950 p-7 text-white shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-wider text-blue-300">Explore the collection</p>
            <h2 class="mt-3 text-2xl font-bold">Find your next great read.</h2>
            <p class="mt-3 max-w-lg text-sm leading-6 text-slate-300">Browse the full catalogue, explore author details, and keep your borrowing history in one place.</p>
            <a href="{{ route('books.index') }}" class="mt-6 inline-flex rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-400">Browse books</a>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-7 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-wider text-slate-500">Quick actions</p>
            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                <a href="{{ route('books.index') }}" class="rounded-xl border border-slate-200 p-4 transition hover:border-blue-300 hover:bg-blue-50">
                    <p class="font-semibold text-slate-900">Browse books</p>
                    <p class="mt-1 text-sm text-slate-500">Search the catalogue.</p>
                </a>
                @if (auth()->user()->role === 'member')
                    <a href="{{ route('my.loans') }}" class="rounded-xl border border-slate-200 p-4 transition hover:border-blue-300 hover:bg-blue-50">
                        <p class="font-semibold text-slate-900">My loans</p>
                        <p class="mt-1 text-sm text-slate-500">Review your history.</p>
                    </a>
                @elseif (in_array(auth()->user()->role, ['librarian', 'admin'], true))
                    <a href="{{ route('desk') }}" class="rounded-xl border border-slate-200 p-4 transition hover:border-blue-300 hover:bg-blue-50">
                        <p class="font-semibold text-slate-900">Open desk</p>
                        <p class="mt-1 text-sm text-slate-500">Manage loan records.</p>
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
