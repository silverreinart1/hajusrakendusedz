<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BookController extends Controller
{
    
    public function index(Request $request)
    {
        $search = $request->query('search');
        $sort   = in_array($request->query('sort'), ['title', 'author', 'publication_year', 'id'])
                    ? $request->query('sort') : 'id';
        $order  = $request->query('order', 'asc') === 'desc' ? 'desc' : 'asc';
        $limit  = min((int) $request->query('limit', 20), 100);
        $genre  = $request->query('genre');

        $cacheKey = "books_{$search}_{$sort}_{$order}_{$limit}_{$genre}";

        $books = Cache::remember($cacheKey, 300, function () use ($search, $sort, $order, $limit, $genre) {
            return Book::query()
                ->when($search, fn($q) => $q->where(fn($q2) =>
                    $q2->where('title', 'like', "%{$search}%")
                       ->orWhere('author', 'like', "%{$search}%")
                ))
                ->when($genre, fn($q) => $q->where('genre', $genre))
                ->orderBy($sort, $order)
                ->limit($limit)
                ->get();
        });

        return response()->json([
            'data' => $books,
            'meta' => [
                'count'  => $books->count(),
                'limit'  => $limit,
                'sort'   => $sort,
                'order'  => $order,
                'search' => $search,
                'genre'  => $genre,
            ],
        ]);
    }

    public function show(Book $book)
    {
        return response()->json(['data' => $book]);
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'title'            => 'required|string|max:255',
        'image'            => 'nullable|url|max:500',
        'description'      => 'required|string',
        'author'           => 'required|string|max:255',
        'publication_year' => 'required|integer|min:1000|max:2100',
        'genre'            => 'nullable|string|max:100',
        'isbn'             => 'nullable|string|max:20',
    ]);

    // Auto-fill cover from Open Library if no image given but ISBN exists
    if (empty($validated['image']) && !empty($validated['isbn'])) {
        $isbn = preg_replace('/[^0-9X]/', '', $validated['isbn']);
        $coverUrl = "https://covers.openlibrary.org/b/isbn/{$isbn}-L.jpg";
        // Open Library returns a 1x1 gif for missing covers, so we verify size
        $headers = @get_headers($coverUrl, 1);
        if (isset($headers['Content-Length']) && (int)$headers['Content-Length'] > 1000) {
            $validated['image'] = $coverUrl;
        }
    }

    $book = Book::create($validated);
    Cache::flush();

    return response()->json(['data' => $book], 201);
}

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title'            => 'sometimes|string|max:255',
            'image'            => 'nullable|url|max:500',
            'description'      => 'sometimes|string',
            'author'           => 'sometimes|string|max:255',
            'publication_year' => 'sometimes|integer|min:1000|max:2100',
            'genre'            => 'nullable|string|max:100',
            'isbn'             => 'nullable|string|max:20',
        ]);

        $book->update($validated);
        Cache::flush();

        return response()->json(['data' => $book]);
    }

    public function destroy(Book $book)
    {
        $book->delete();
        Cache::flush();
        return response()->json(['message' => 'Raamat kustutatud.']);
    }
}
