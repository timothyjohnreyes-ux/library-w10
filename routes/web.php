<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProfileController;
use App\Models\Book;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/locale', [LocaleController::class, 'update'])->name('locale.update');

Route::get('/dashboard', function () {
    $stats = [
        'books' => Book::count(),
        'members' => User::where('role', 'member')->count(),
        'active_loans' => DB::table('book_member')->whereNull('returned_at')->count(),
        'returned_loans' => DB::table('book_member')->whereNotNull('returned_at')->count(),
    ];

    return view('dashboard', compact('stats'));
})->middleware(['auth', 'role:admin', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
    Route::get('/my/loans', [LoanController::class, 'index'])
        ->middleware('role:member')
        ->name('my.loans');

    Route::get('/admin/users', [AdminUserController::class, 'index'])
        ->middleware('role:admin')
        ->name('admin.users');

    Route::patch('/admin/users/{user}/role', [
        AdminUserController::class,
        'updateRole',
    ])
        ->middleware('role:admin')
        ->name('admin.users.update-role');

    Route::patch('/desk/{member}/{book}/return', [
        LoanController::class,
        'markReturned',
    ])
        ->middleware('role:librarian')
        ->name('loans.return');
});

Route::post('/books/{book}/borrow', [LoanController::class, 'store'])
    ->middleware(['auth', 'role:member'])
    ->name('books.borrow');

Route::get('/desk', [LoanController::class, 'desk'])
    ->middleware(['auth', 'role:librarian,admin'])
    ->name('desk');

Route::get('/books', [BookController::class, 'index'])->name('books.index');

Route::middleware(['auth', 'role:librarian,admin'])->group(function () {
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
    Route::patch('/books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
});

Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
