<x-app-layout>
    <x-slot name="header"><div><p class="text-sm font-semibold uppercase tracking-wider text-blue-600">Catalogue</p><h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Edit book</h1></div></x-slot>
    @include('books.partials.form', ['formAction' => route('books.update', $book), 'formMethod' => 'PATCH', 'submitLabel' => 'Save changes', 'book' => $book])
</x-app-layout>
