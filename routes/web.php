<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['api.auth'])->name('dashboard');

Route::middleware('api.auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;

Route::get('authors', [AuthorController::class, 'index'])->name('authors.index');
Route::get('authors/export/csv', [AuthorController::class, 'exportCsv'])->name('authors.export.csv');
Route::get('authors/export/pdf', [AuthorController::class, 'exportPdf'])->name('authors.export.pdf');
Route::middleware('api.auth')->group(function () {
    Route::get('authors/create', [AuthorController::class, 'create'])->name('authors.create');
    Route::post('authors', [AuthorController::class, 'store'])->name('authors.store');
    Route::get('authors/{author}/edit', [AuthorController::class, 'edit'])->name('authors.edit');
    Route::match(['put', 'patch'], 'authors/{author}', [AuthorController::class, 'update'])->name('authors.update');
    Route::delete('authors/{author}', [AuthorController::class, 'destroy'])->name('authors.destroy');
});

Route::get('authors/{author}', [AuthorController::class, 'show'])->name('authors.show');

Route::get('books', [BookController::class, 'index'])->name('books.index');
Route::get('books/export/csv', [BookController::class, 'exportCsv'])->name('books.export.csv');
Route::get('books/export/pdf', [BookController::class, 'exportPdf'])->name('books.export.pdf');
Route::middleware('api.auth')->group(function () {
    Route::get('books/create', [BookController::class, 'create'])->name('books.create');
    Route::post('books', [BookController::class, 'store'])->name('books.store');
    Route::get('books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
    Route::match(['put', 'patch'], 'books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
});

Route::get('books/{book}', [BookController::class, 'show'])->name('books.show');


Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('categories/export/csv', [CategoryController::class, 'exportCsv'])->name('categories.export.csv');
Route::get('categories/export/pdf', [CategoryController::class, 'exportPdf'])->name('categories.export.pdf');
Route::middleware('api.auth')->group(function () {
    Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::match(['put', 'patch'], 'categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});

Route::get('categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
