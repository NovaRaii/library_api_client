<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $response = Http::api()->withToken($this->token)->get('/authors');

            if ($response->failed()) {
                return redirect()->route('dashboard')->with('error', 'Hiba történt a szerzők betöltésekor.');
            }

            $authors = $response->json();
            if (isset($authors['authors'])) {
                $authors = $authors['authors'];
            } elseif (isset($authors['data'])) {
                $authors = $authors['data'];
            }

            // Convert arrays to objects
            $authors = collect($authors)->map(function ($item) {
                return (object) $item;
            });

            return view('authors.index', ['authors' => $authors, 'isAuthenticated' => $this->isAuthenticated()]);

        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('authors.create', ['isAuthenticated' => $this->isAuthenticated()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $response = Http::api()->withToken($this->token)->post('/authors', [
                'name' => $request->input('name'),
                'nationality' => $request->input('nationality'),
                'age' => $request->input('age'),
                'gender' => $request->input('gender'),
            ]);

            if ($response->failed()) {
                return back()->with('error', 'Nem sikerült létrehozni a szerzőt: ' . $response->body());
            }

            return redirect()->route('authors.index')->with('success', 'Szerző sikeresen létrehozva');

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
            $response = Http::api()->withToken($this->token)->get("/authors/{$id}");

            if ($response->failed()) {
                return redirect()->route('authors.index')->with('error', 'Nem található a szerző.');
            }

            $author = $response->json();
            if (isset($author['author'])) {
                $author = $author['author'];
            } elseif (isset($author['data'])) {
                $author = $author['data'];
            }
            $author = (object) $author;

            return view('authors.show', ['author' => $author, 'isAuthenticated' => $this->isAuthenticated()]);

        } catch (\Exception $e) {
            return redirect()->route('authors.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $response = Http::api()->withToken($this->token)->get("/authors/{$id}");

            if ($response->failed()) {
                return redirect()->route('authors.index')->with('error', 'Nem található a szerző.');
            }

            $author = $response->json();
            if (isset($author['author'])) {
                $author = $author['author'];
            } elseif (isset($author['data'])) {
                $author = $author['data'];
            }
            $author = (object) $author;

            return view('authors.edit', ['author' => $author, 'isAuthenticated' => $this->isAuthenticated()]);

        } catch (\Exception $e) {
            return redirect()->route('authors.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $response = Http::api()->withToken($this->token)->put("/authors/{$id}", [
                'name' => $request->input('name'),
                'nationality' => $request->input('nationality'),
                'age' => $request->input('age'),
                'gender' => $request->input('gender'),
            ]);

            if ($response->failed()) {
                return back()->with('error', 'Nem sikerült frissíteni a szerzőt.');
            }

            return redirect()->route('authors.index')->with('success', 'Szerző sikeresen módosítva');

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
            $response = Http::api()->withToken($this->token)->delete("/authors/{$id}");

            if ($response->failed()) {
                return back()->with('error', 'Nem sikerült törölni a szerzőt.');
            }

            return redirect()->route('authors.index')->with('success', 'Szerző sikeresen törölve');

        } catch (\Exception $e) {
            return back()->with('error', 'Hiba: ' . $e->getMessage());
        }
    }
}
