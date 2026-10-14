<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'LibraryHub') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-950 font-sans text-white">
        <main class="min-h-screen">
            <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-6 sm:px-6 lg:px-8">
                <a href="{{ route('books.index') }}" class="flex items-center gap-3"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500 text-lg font-bold">L</span><span class="text-lg font-semibold">LibraryHub</span></a>
                <div class="flex items-center gap-3"><a href="{{ route('books.index') }}" class="hidden rounded-lg px-3 py-2 text-sm font-semibold text-slate-300 hover:bg-white/10 sm:inline">Browse books</a>@auth @if (auth()->user()->role === 'admin')<a href="{{ route('dashboard') }}" class="rounded-lg bg-blue-500 px-3 py-2 text-sm font-semibold hover:bg-blue-400">Dashboard</a>@endif @else<a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-300 hover:bg-white/10">Log in</a><a href="{{ route('register') }}" class="rounded-lg bg-blue-500 px-3 py-2 text-sm font-semibold hover:bg-blue-400">Create account</a>@endauth</div>
            </nav>
            <section class="mx-auto grid max-w-7xl items-center gap-12 px-4 pb-24 pt-20 sm:px-6 lg:grid-cols-[1.1fr_0.9fr] lg:px-8 lg:pt-28">
                <div><p class="text-sm font-semibold uppercase tracking-[0.2em] text-blue-300">Your modern library workspace</p><h1 class="mt-6 max-w-3xl text-4xl font-bold tracking-tight sm:text-6xl">A simpler way to discover, borrow, and manage books.</h1><p class="mt-6 max-w-2xl text-lg leading-8 text-slate-300">LibraryHub brings your catalogue, member loans, and staff operations together in one clear and reliable place.</p><div class="mt-8 flex flex-wrap gap-3"><a href="{{ route('books.index') }}" class="rounded-lg bg-blue-500 px-5 py-3 text-sm font-semibold hover:bg-blue-400">Explore the catalogue</a>@guest<a href="{{ route('register') }}" class="rounded-lg border border-white/20 px-5 py-3 text-sm font-semibold text-slate-200 hover:bg-white/10">Join the library</a>@endguest</div></div>
                <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl"><div class="rounded-2xl bg-white p-6 text-slate-900"><div class="flex items-center justify-between"><p class="text-sm font-semibold text-slate-500">Library snapshot</p><span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Ready to explore</span></div><div class="mt-8 grid grid-cols-2 gap-4"><div class="rounded-xl bg-slate-50 p-4"><p class="text-2xl font-bold">24/7</p><p class="mt-1 text-sm text-slate-500">Catalogue access</p></div><div class="rounded-xl bg-slate-50 p-4"><p class="text-2xl font-bold">1 place</p><p class="mt-1 text-sm text-slate-500">For every loan</p></div></div><div class="mt-4 rounded-xl bg-slate-950 p-4 text-white"><p class="text-sm font-semibold">Built for every role</p><p class="mt-1 text-sm text-slate-400">Members, librarians, and administrators each get the tools they need.</p></div></div></div>
            </section>
        </main>
    </body>
</html>
