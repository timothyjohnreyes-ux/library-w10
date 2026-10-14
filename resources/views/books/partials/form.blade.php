<form action="{{ $formAction }}" method="POST" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
    @csrf
    @if ($formMethod !== 'POST') @method($formMethod) @endif
    <div class="grid gap-6 sm:grid-cols-2">
        <div class="sm:col-span-2"><label for="title" class="text-sm font-semibold text-slate-700">Title</label><input id="title" name="title" value="{{ old('title', $book?->title) }}" required class="mt-2 w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500"></div>
        <div><label for="author_id" class="text-sm font-semibold text-slate-700">Author</label><select id="author_id" name="author_id" required class="mt-2 w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500"><option value="">Select author</option>@foreach ($authors as $author)<option value="{{ $author->id }}" @selected(old('author_id', $book?->author_id) == $author->id)>{{ $author->name }}</option>@endforeach</select></div>
        <div><label for="isbn" class="text-sm font-semibold text-slate-700">ISBN</label><input id="isbn" name="isbn" value="{{ old('isbn', $book?->isbn) }}" maxlength="13" required class="mt-2 w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500"></div>
        <div><label for="published_year" class="text-sm font-semibold text-slate-700">Published year</label><input id="published_year" name="published_year" type="number" min="1000" max="2100" value="{{ old('published_year', $book?->published_year) }}" required class="mt-2 w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500"></div>
        <label class="flex items-center gap-3 self-end pb-2"><input type="checkbox" name="is_reference" value="1" @checked(old('is_reference', $book?->is_reference)) class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"><span class="text-sm font-semibold text-slate-700">Reference book</span></label>
    </div>
    <div class="mt-8 flex flex-wrap gap-3"><button class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">{{ $submitLabel }}</button><a href="{{ route('books.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a></div>
</form>
