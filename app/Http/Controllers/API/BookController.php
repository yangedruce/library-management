<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $books = Book::with('categories')->get();
        return response()->json($books);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'author' => 'nullable|string',
            'description' => 'nullable|string',
            'categories' => 'array'
        ]);

        $book = Book::create($data);
        $book->categories()->sync($data['categories'] ?? []);

        return response()->json($book->load('categories'));
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string',
            'author' => 'nullable|string',
            'description' => 'nullable|string',
            'categories' => 'array'
        ]);

        $book->update($data);
        $book->categories()->sync($data['categories'] ?? []);

        return response()->json($book->load('categories'));
    }

    public function destroy($id)
    {
        Book::findOrFail($id)->delete();
        return response()->json(['message' => 'Book has been deleted']);
    }
}
