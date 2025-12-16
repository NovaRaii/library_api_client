<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // Fetch all data first
            $response = Http::api()->get('/books');
            $data = $response->json();
            $books = collect($data['books'] ?? ($data['data'] ?? $data));
            
            $catResponse = Http::api()->get('/categories');
            $catData = $catResponse->json();
            $categories = collect($catData['categories'] ?? ($catData['data'] ?? $catData));
            
            $authorsResponse = Http::api()->get('/authors');
            $authorsData = $authorsResponse->json();
            $authors = collect($authorsData['authors'] ?? ($authorsData['data'] ?? $authorsData));

            // Convert raw arrays to objects for consistent handling
            $books = $books->map(function ($item) {
                return (object) $item;
            });
            $categories = $categories->map(function ($item) {
                return (object) $item;
            });
            $authors = $authors->map(function ($item) {
                return (object) $item;
            });

            // Client-side Filtering
            
            // 1. Category Filter
            if ($request->filled('category')) {
                $categoryId = $request->input('category');
                $books = $books->filter(function ($book) use ($categoryId) {
                    return isset($book->category_id) && $book->category_id == $categoryId;
                });
            }

            // 2. Search Filter (Name or Author Name)
            if ($request->filled('search')) {
                $searchTerm = strtolower($request->input('search'));
                $books = $books->filter(function ($book) use ($searchTerm, $authors) {
                    // Match book name
                    if (str_contains(strtolower($book->name), $searchTerm)) {
                        return true;
                    }
                    // Match book ID
                    if (str_contains((string)$book->id, $searchTerm)) {
                        return true;
                    }
                    // Match author name
                    $author = $authors->firstWhere('id', $book->author_id);
                    if ($author && str_contains(strtolower($author->name), $searchTerm)) {
                        return true;
                    }
                    return false;
                });
            }
            
            return view('books.index', [
                'books' => $books, 
                'categories' => $categories, 
                'authors' => $authors,
                'category' => $request->input('category'),
                'search' => $request->input('search'),
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

    /**
     * Export books to CSV
     */
    public function exportCsv(Request $request)
    {
        try {
            $queryParams = $request->all();
            $response = Http::api()->get('/books', $queryParams);

            if ($response->failed()) {
                return back()->with('error', 'Hiba történt az exportálás során.');
            }

            $data = $response->json();
            $books = $data['books'] ?? ($data['data'] ?? $data);

            // Get authors and categories
            $authorsResponse = Http::api()->get('/authors');
            $authorsData = $authorsResponse->json();
            $authors = collect($authorsData['authors'] ?? ($authorsData['data'] ?? $authorsData));

            $catResponse = Http::api()->get('/categories');
            $catData = $catResponse->json();
            $categories = collect($catData['categories'] ?? ($catData['data'] ?? $catData));

            // Create CSV content
            $csvData = [];
            $csvData[] = ['ID', 'Cím', 'Szerző', 'Kategória', 'ISBN', 'Kiadás', 'Megjelenés', 'Ár'];

            foreach ($books as $book) {
                $book = (object) $book;
                $author = $authors->firstWhere('id', $book->author_id);
                $category = $categories->firstWhere('id', $book->category_id);

                $csvData[] = [
                    $book->id,
                    $book->name,
                    $author ? $author['name'] : 'Ismeretlen',
                    $category ? $category['name'] : 'Ismeretlen',
                    $book->isbn ?? '',
                    $book->edition ?? '',
                    $book->publication_date ?? '',
                    $book->price ?? ''
                ];
            }

            // Generate CSV
            $filename = 'books_' . date('Y-m-d_His') . '.csv';
            $handle = fopen('php://temp', 'r+');
            
            foreach ($csvData as $row) {
                fputcsv($handle, $row);
            }
            
            rewind($handle);
            $csv = stream_get_contents($handle);
            fclose($handle);

            return Response::make($csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Hiba: ' . $e->getMessage());
        }
    }

    /**
     * Export books to PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            $queryParams = $request->all();
            $response = Http::api()->get('/books', $queryParams);

            if ($response->failed()) {
                return back()->with('error', 'Hiba történt az exportálás során.');
            }

            $data = $response->json();
            $books = collect($data['books'] ?? ($data['data'] ?? $data))->map(function ($item) {
                return (object) $item;
            });

            // Get authors and categories
            $authorsResponse = Http::api()->get('/authors');
            $authorsData = $authorsResponse->json();
            $authors = collect($authorsData['authors'] ?? ($authorsData['data'] ?? $authorsData))->map(function ($item) {
                return (object) $item;
            });

            $catResponse = Http::api()->get('/categories');
            $catData = $catResponse->json();
            $categories = collect($catData['categories'] ?? ($catData['data'] ?? $catData))->map(function ($item) {
                return (object) $item;
            });

            $pdf = Pdf::loadView('books.pdf', [
                'books' => $books,
                'authors' => $authors,
                'categories' => $categories,
                'exportDate' => date('Y-m-d H:i:s')
            ]);

            return $pdf->download('books_' . date('Y-m-d_His') . '.pdf');

        } catch (\Exception $e) {
            return back()->with('error', 'Hiba: ' . $e->getMessage());
        }
    }
}
