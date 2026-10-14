<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $member = $user->member;

        if (! $member) {
            abort(403, 'Your account is not linked to a member record.');
        }

        Gate::authorize('view', $member);

        $books = $member->books()
            ->with('author')
            ->orderByPivot('borrowed_at', 'desc')
            ->paginate(10);

        return view('my.loans', compact('books'));
    }

    public function store(Request $request, Book $book)
    {
        $user = $request->user();

        Gate::authorize('view', $book);

        if ($user->role !== 'member') {
            abort(403, 'Only members can borrow books.');
        }

        $member = $user->member;

        if (! $member) {
            abort(403, 'Your account is not linked to a member record.');
        }

        if ($book->is_reference) {
            throw ValidationException::withMessages([
                'book' => 'Reference books cannot be borrowed.',
            ]);
        }

        $bookIsBorrowed = DB::table('book_member')
            ->where('book_id', $book->id)
            ->whereNull('returned_at')
            ->exists();

        if ($bookIsBorrowed) {
            throw ValidationException::withMessages([
                'book' => 'This book is currently borrowed and cannot be borrowed again until it is returned.',
            ]);
        }

        $alreadyBorrowed = $member->books()
            ->where('books.id', $book->id)
            ->wherePivotNull('returned_at')
            ->exists();

        if ($alreadyBorrowed) {
            throw ValidationException::withMessages([
                'book' => 'You already have this book borrowed.',
            ]);
        }

        $member->books()->attach($book->id, [
            'borrowed_at' => now(),
            'returned_at' => null,
        ]);

        return redirect()
            ->route('books.show', $book)
            ->with('success', 'Book borrowed successfully!');
    }

    public function desk(Request $request): View
    {
        Gate::authorize('viewAny', Member::class);

        $status = $request->validate([
            'status' => ['nullable', 'in:all,active,returned'],
        ])['status'] ?? 'all';

        $loanQuery = DB::table('book_member')
            ->join('books', 'books.id', '=', 'book_member.book_id')
            ->join('authors', 'authors.id', '=', 'books.author_id')
            ->join('members', 'members.id', '=', 'book_member.member_id')
            ->select([
                'book_member.id',
                'book_member.member_id',
                'book_member.book_id',
                'book_member.borrowed_at',
                'book_member.returned_at',
                'books.title as book_title',
                'authors.name as author_name',
                'members.name as member_name',
            ]);

        if ($status === 'active') {
            $loanQuery->whereNull('book_member.returned_at');
        }

        if ($status === 'returned') {
            $loanQuery->whereNotNull('book_member.returned_at');
        }

        $loans = $loanQuery
            ->orderByDesc('book_member.borrowed_at')
            ->paginate(10)
            ->withQueryString();

        return view('desk', [
            'loans' => $loans,
            'status' => $status,
            'loanCount' => $loans->total(),
        ]);
    }

    public function markReturned(Member $member, Book $book)
    {
        Gate::authorize('markReturned', $member);

        $loan = DB::table('book_member')
            ->where('member_id', $member->id)
            ->where('book_id', $book->id)
            ->whereNull('returned_at')
            ->latest('id')
            ->first();

        if (! $loan) {
            return back()->with('error', 'No active loan found for this book.');
        }

        DB::table('book_member')
            ->where('id', $loan->id)
            ->update([
                'returned_at' => now(),
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Book marked as returned.');
    }
}
