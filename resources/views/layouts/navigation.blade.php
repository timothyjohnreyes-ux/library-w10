@auth
<nav x-data="{ open: false }" class="bg-slate-950 text-white shadow-lg">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-4 py-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-8">
            <a href="{{ auth()->user()->role === 'admin' ? route('dashboard') : route('books.index') }}" class="flex items-center gap-3" aria-label="LibraryHub">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500 text-lg font-bold">L</span>
                <span class="hidden text-lg font-semibold tracking-tight sm:inline">LibraryHub</span>
            </a>

            <div class="hidden items-center gap-1 md:flex">
                @if (auth()->user()->role === 'admin')
                    <a href="{{ route('dashboard') }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">{{ __('messages.dashboard') }}</a>
                @endif
                <a href="{{ route('books.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('books.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">{{ __('messages.books') }}</a>
                @if (auth()->user()->role === 'member')
                    <a href="{{ route('my.loans') }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('my.loans') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">{{ __('messages.my_loans') }}</a>
                @endif
                @if (in_array(auth()->user()->role, ['librarian', 'admin'], true))
                    <a href="{{ route('desk') }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('desk') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">{{ __('messages.librarian_desk') }}</a>
                @endif
                @if (auth()->user()->role === 'admin')
                    <a href="{{ route('admin.users') }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.users*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">{{ __('messages.users') }}</a>
                @endif
            </div>
        </div>

        <div class="hidden items-center gap-4 md:flex">
            <div class="text-right">
                <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                <p class="text-xs capitalize text-slate-400">{{ auth()->user()->role }}</p>
            </div>
            <a href="{{ route('profile.edit') }}" class="rounded-lg border border-white/15 px-3 py-2 text-sm text-slate-200 hover:bg-white/10">{{ __('messages.profile') }}</a>
            <form method="POST" action="{{ route('locale.update') }}" class="flex items-center gap-1 rounded-lg border border-white/15 p-1">
                @csrf
                <input type="hidden" name="locale" value="{{ app()->getLocale() === 'en' ? 'fil' : 'en' }}">
                <button type="submit" class="px-2 py-1 text-xs font-semibold text-slate-200 hover:text-white">{{ app()->getLocale() === 'en' ? 'Filipino' : 'English' }}</button>
            </form>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="rounded-lg bg-blue-500 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-400">{{ __('messages.log_out') }}</button>
            </form>
        </div>

        <button @click="open = !open" class="rounded-lg p-2 text-slate-300 hover:bg-white/10 md:hidden" aria-label="Toggle navigation">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div x-show="open" x-cloak class="border-t border-white/10 px-4 pb-4 md:hidden">
        <div class="grid gap-1 pt-3">
            @if (auth()->user()->role === 'admin')
                <a href="{{ route('dashboard') }}" class="rounded-lg px-3 py-2 text-sm text-slate-200 hover:bg-white/10">{{ __('messages.dashboard') }}</a>
            @endif
            <a href="{{ route('books.index') }}" class="rounded-lg px-3 py-2 text-sm text-slate-200 hover:bg-white/10">{{ __('messages.books') }}</a>
            @if (auth()->user()->role === 'member') <a href="{{ route('my.loans') }}" class="rounded-lg px-3 py-2 text-sm text-slate-200 hover:bg-white/10">{{ __('messages.my_loans') }}</a> @endif
            @if (in_array(auth()->user()->role, ['librarian', 'admin'], true)) <a href="{{ route('desk') }}" class="rounded-lg px-3 py-2 text-sm text-slate-200 hover:bg-white/10">{{ __('messages.librarian_desk') }}</a> @endif
            @if (auth()->user()->role === 'admin') <a href="{{ route('admin.users') }}" class="rounded-lg px-3 py-2 text-sm text-slate-200 hover:bg-white/10">{{ __('messages.users') }}</a> @endif
            <a href="{{ route('profile.edit') }}" class="rounded-lg px-3 py-2 text-sm text-slate-200 hover:bg-white/10">{{ __('messages.profile') }}</a>
            <form method="POST" action="{{ route('locale.update') }}">@csrf<input type="hidden" name="locale" value="{{ app()->getLocale() === 'en' ? 'fil' : 'en' }}"><button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm text-blue-300 hover:bg-white/10">{{ app()->getLocale() === 'en' ? __('messages.filipino') : __('messages.english') }}</button></form>
            <form method="POST" action="{{ route('logout') }}" class="pt-2">
                @csrf
                <button type="submit" class="w-full rounded-lg bg-blue-500 px-3 py-2 text-left text-sm font-semibold">Log out</button>
            </form>
        </div>
    </div>
</nav>
@else
<nav class="bg-slate-950 text-white shadow-lg">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
        <a href="{{ route('books.index') }}" class="flex items-center gap-3"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500 text-lg font-bold">L</span><span class="text-lg font-semibold">LibraryHub</span></a>
        <div class="flex items-center gap-3"><a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-200 hover:bg-white/10">Log in</a><a href="{{ route('register') }}" class="rounded-lg bg-blue-500 px-3 py-2 text-sm font-semibold hover:bg-blue-400">Create account</a></div>
    </div>
</nav>
@endauth
