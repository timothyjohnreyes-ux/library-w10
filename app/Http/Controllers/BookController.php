<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Book::class);

        $books = Book::with('author')
            ->addSelect([
                'active_loan_member_id' => DB::table('book_member')
                    ->select('member_id')
                    ->whereColumn('book_member.book_id', 'books.id')
                    ->whereNull('book_member.returned_at')
                    ->limit(1),
            ])
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();

                $query->where(function ($query) use ($search): void {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%")
                        ->orWhereHas('author', fn ($query) => $query->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('books.index', compact('books'));
    }

    public function show(Book $book): View
    {
        Gate::authorize('view', $book);

        $book->load('author');

        $activeLoan = DB::table('book_member')
            ->where('book_id', $book->id)
            ->whereNull('returned_at')
            ->first(['member_id']);

        $currentMemberId = request()->user()?->member?->id;

        return view('books.show', [
            'book' => $book,
            'activeLoan' => $activeLoan,
            'currentMemberId' => $currentMemberId,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Book::class);

        $authors = Author::orderBy('name')->get();

        return view('books.create', compact('authors'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Book::class);

        $validated = $request->validate([
            'author_id' => ['required', 'exists:authors,id'],
            'title' => ['required', 'string', 'max:255'],
            'isbn' => ['required', 'string', 'size:13', 'unique:books,isbn'],
            'published_year' => ['required', 'integer', 'between:1000,2100'],
            'is_reference' => ['sometimes', 'boolean'],
        ]);

        $validated['is_reference'] = $request->boolean('is_reference');
        $book = Book::create($validated);

        return redirect()->route('books.show', $book)
            ->with('success', 'Book added successfully.');
    }

    public function edit(Book $book): View
    {
        Gate::authorize('update', $book);

        $authors = Author::orderBy('name')->get();

        return view('books.edit', compact('book', 'authors'));
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        Gate::authorize('update', $book);

        $validated = $request->validate([
            'author_id' => ['required', 'exists:authors,id'],
            'title' => ['required', 'string', 'max:255'],
            'isbn' => ['required', 'string', 'size:13', 'unique:books,isbn,'.$book->id],
            'published_year' => ['required', 'integer', 'between:1000,2100'],
            'is_reference' => ['sometimes', 'boolean'],
        ]);

        $validated['is_reference'] = $request->boolean('is_reference');
        $book->update($validated);

        return redirect()->route('books.show', $book)
            ->with('success', 'Book updated successfully.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        Gate::authorize('delete', $book);

        $book->delete();

        return redirect()->route('books.index')
            ->with('success', 'Book deleted successfully.');
    }
}
