<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $queryParams = $request->all();
            $response = Http::api()->get('/books', $queryParams);

            if ($response->failed()) {
                return redirect()->route('dashboard')->with('error', 'Hiba történt a könyvek betöltésekor.');
            }

            $data = $response->json();
            $books = $data['books'] ?? ($data['data'] ?? $data);
            
            $catResponse = Http::api()->get('/categories');
            $catData = $catResponse->json();
            $categories = $catData['categories'] ?? ($catData['data'] ?? $catData);
            
            $books = collect($books)->map(function ($item) {
                return (object) $item;
            });
            $categories = collect($categories)->map(function ($item) {
                return (object) $item;
            });
            
            $authorsResponse = Http::api()->get('/authors');
            $authorsData = $authorsResponse->json();
            $authors = $authorsData['authors'] ?? ($authorsData['data'] ?? $authorsData);
            
            $authors = collect($authors)->map(function ($item) {
                return (object) $item;
            });
            
            return view('books.index', [
                'books' => $books, 
                'categories' => $categories, 
                'authors' => $authors,
                'category' => $request->input('category'),
                'isAuthenticated' => $this->isAuthenticated()
            ]);

        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $authorsResponse = Http::api()->get('/authors');
            $categoriesResponse = Http::api()->get('/categories');
            
            $authorsData = $authorsResponse->json();
            $authors = $authorsData['authors'] ?? ($authorsData['data'] ?? $authorsData);

            $categoriesData = $categoriesResponse->json();
            $categories = $categoriesData['categories'] ?? ($categoriesData['data'] ?? $categoriesData);
            
            $authors = collect($authors)->map(function ($item) {
                return (object) $item;
            });
            $categories = collect($categories)->map(function ($item) {
                return (object) $item;
            });

            $booksResponse = Http::api()->get('/books');
            $booksData = $booksResponse->json();
            $allBooks = $booksData['books'] ?? ($booksData['data'] ?? $booksData);
            $nextId = count($allBooks) + 1;
            
            // Format: 978-1-00000-000-{id}
            $suggestedIsbn = '978-1-00001-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

            return view('books.create', compact('authors', 'categories', 'suggestedIsbn') + ['isAuthenticated' => $this->isAuthenticated()]);
        } catch (\Exception $e) {
            return back()->with('error', 'Hiba: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = [
                'name' => $request->input('name'),
                'price' => $request->input('price'),
                'edition' => $request->input('edition'),
                'publication_date' => $request->input('publication_date'),
                'category_id' => $request->input('category_id'),
                'author_id' => $request->input('author_id'),
                'isbn' => $request->input('isbn'),
            ];

            $requestHttp = Http::api()->withToken($this->token);
            
            if ($request->hasFile('cover')) {
                $file = $request->file('cover');
                $requestHttp->attach('cover', file_get_contents($file), $file->getClientOriginalName());
            }

            $response = $requestHttp->post('/books', $data);

            if ($response->failed()) {
                return back()->with('error', 'Nem sikerült létrehozni a könyvet: ' . $response->body());
            }

            return redirect()->route('books.index')->with('success', 'Könyv sikeresen létrehozva');

        } catch (\Exception $e) {
            return back()->with('error', 'Hiba: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $response = Http::api()->withToken($this->token)->get("/books/{$id}");

            if ($response->failed()) {
                return redirect()->route('books.index')->with('error', 'Nem található a könyv.');
            }

            $book = $response->json();
            if (isset($book['book'])) {
                $book = $book['book'];
            } elseif (isset($book['data'])) {
                $book = $book['data'];
            }
            $book = (object) $book;

            return view('books.show', ['book' => $book, 'isAuthenticated' => $this->isAuthenticated()]);

        } catch (\Exception $e) {
            return redirect()->route('books.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $bookResponse = Http::api()->withToken($this->token)->get("/books/{$id}");
            $authorsResponse = Http::api()->get('/authors');
            $categoriesResponse = Http::api()->get('/categories');

            if ($bookResponse->failed()) {
                return redirect()->route('books.index')->with('error', 'Nem található a könyv.');
            }

            $book = $bookResponse->json();
            if (isset($book['book'])) {
                $book = $book['book'];
            } elseif (isset($book['data'])) {
                $book = $book['data'];
            }
            $book = (object) $book;

            $authorsData = $authorsResponse->json();
            $authors = $authorsData['authors'] ?? ($authorsData['data'] ?? $authorsData);

            $categoriesData = $categoriesResponse->json();
            $categories = $categoriesData['categories'] ?? ($categoriesData['data'] ?? $categoriesData);
            
            $authors = collect($authors)->map(function ($item) {
                return (object) $item;
            });
            $categories = collect($categories)->map(function ($item) {
                return (object) $item;
            });

            return view('books.edit', compact('book', 'authors', 'categories') + ['isAuthenticated' => $this->isAuthenticated()]);

        } catch (\Exception $e) {
            return redirect()->route('books.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $data = [
                'name' => $request->input('name'),
                'price' => $request->input('price'),
                'edition' => $request->input('edition'),
                'publication_date' => $request->input('publication_date'),
                'category_id' => $request->input('category_id'),
                'author_id' => $request->input('author_id'),
                'isbn' => $request->input('isbn'),
                '_method' => 'PUT',
            ];

            $requestHttp = Http::api()->withToken($this->token);

            if ($request->hasFile('cover')) {
                $file = $request->file('cover');
                $requestHttp->attach('cover', file_get_contents($file), $file->getClientOriginalName());
                // Use POST with _method=PUT for file uploads
                $response = $requestHttp->post("/books/{$id}", $data);
            } else {
                 $response = $requestHttp->put("/books/{$id}", $data);
            }

            if ($response->failed()) {
                return back()->with('error', 'Nem sikerült frissíteni a könyvet: ' . $response->body());
            }

            return redirect()->route('books.index')->with('success', 'Könyv sikeresen módosítva');

        } catch (\Exception $e) {
            return back()->with('error', 'Hiba: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $response = Http::api()->withToken($this->token)->delete("/books/{$id}");

            if ($response->failed()) {
                return back()->with('error', 'Nem sikerült törölni a könyvet.');
            }

            return redirect()->route('books.index')->with('success', 'Könyv sikeresen törölve');

        } catch (\Exception $e) {
            return back()->with('error', 'Hiba: ' . $e->getMessage());
        }
    }
}
