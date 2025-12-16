<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $response = Http::api()->get('/categories');

            if ($response->failed()) {
                return redirect()->route('dashboard')->with('error', 'Hiba történt a kategóriák betöltésekor.');
            }

            $categories = $response->json();
            if (isset($categories['categories'])) {
                $categories = $categories['categories'];
            } elseif (isset($categories['data'])) {
                $categories = $categories['data'];
            }
            $categories = collect($categories)->map(function ($item) {
                return (object) $item;
            });

            return view('categories.index', ['categories' => $categories, 'isAuthenticated' => $this->isAuthenticated()]);

        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create', ['isAuthenticated' => $this->isAuthenticated()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $response = Http::api()->withToken($this->token)->post('/categories', [
                'name' => $request->input('name'),
            ]);

            if ($response->failed()) {
                return back()->with('error', 'Nem sikerült létrehozni a kategóriát.');
            }

            return redirect()->route('categories.index')->with('success', 'Kategória sikeresen létrehozva');

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
            $response = Http::api()->withToken($this->token)->get("/categories/{$id}");

            if ($response->failed()) {
                return redirect()->route('categories.index')->with('error', 'Nem található a kategória.');
            }

            $category = $response->json();
            if (isset($category['category'])) {
                $category = $category['category'];
            } elseif (isset($category['data'])) {
                $category = $category['data'];
            }
            $category = (object) $category;

            return view('categories.show', ['category' => $category, 'isAuthenticated' => $this->isAuthenticated()]);

        } catch (\Exception $e) {
            return redirect()->route('categories.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $response = Http::api()->withToken($this->token)->get("/categories/{$id}");

            if ($response->failed()) {
                return redirect()->route('categories.index')->with('error', 'Nem található a kategória.');
            }

            $category = $response->json();
            if (isset($category['category'])) {
                $category = $category['category'];
            } elseif (isset($category['data'])) {
                $category = $category['data'];
            }
            $category = (object) $category;

            return view('categories.edit', ['category' => $category, 'isAuthenticated' => $this->isAuthenticated()]);

        } catch (\Exception $e) {
            return redirect()->route('categories.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $response = Http::api()->withToken($this->token)->put("/categories/{$id}", [
                'name' => $request->input('name'),
            ]);

            if ($response->failed()) {
                return back()->with('error', 'Nem sikerült frissíteni a kategóriát.');
            }

            return redirect()->route('categories.index')->with('success', 'Kategória sikeresen módosítva');

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
            $response = Http::api()->withToken($this->token)->delete("/categories/{$id}");

            if ($response->failed()) {
                return back()->with('error', 'Nem sikerült törölni a kategóriát.');
            }

            return redirect()->route('categories.index')->with('success', 'Kategória sikeresen törölve');

        } catch (\Exception $e) {
            return back()->with('error', 'Hiba: ' . $e->getMessage());
        }
    }
    /**
     * Export categories to CSV
     */
    public function exportCsv(Request $request)
    {
        try {
            $response = Http::api()->get('/categories');

            if ($response->failed()) {
                return back()->with('error', 'Hiba történt az exportálás során.');
            }

            $categories = $response->json();
            $categories = collect($categories['categories'] ?? ($categories['data'] ?? $categories))->map(function ($item) {
                return (object) $item;
            });

            // Create CSV content
            $csvData = [];
            $csvData[] = ['ID', 'Név'];

            foreach ($categories as $category) {
                $csvData[] = [
                    $category->id,
                    $category->name
                ];
            }

            // Generate CSV
            $filename = 'categories_' . date('Y-m-d_His') . '.csv';
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
     * Export categories to PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            $response = Http::api()->get('/categories');

            if ($response->failed()) {
                return back()->with('error', 'Hiba történt az exportálás során.');
            }

            $categories = $response->json();
            $categories = collect($categories['categories'] ?? ($categories['data'] ?? $categories))->map(function ($item) {
                return (object) $item;
            });

            $pdf = Pdf::loadView('categories.pdf', [
                'categories' => $categories,
                'exportDate' => date('Y-m-d H:i:s')
            ]);

            return $pdf->download('categories_' . date('Y-m-d_His') . '.pdf');

        } catch (\Exception $e) {
            return back()->with('error', 'Hiba: ' . $e->getMessage());
        }
    }
}
