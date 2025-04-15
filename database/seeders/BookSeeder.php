<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Book;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            ['title' => 'The Great Gatsby', 'author' => 'F. Scott Fitzgerald', 'description' => 'A story set in the Roaring Twenties.'],
            ['title' => 'A Brief History of Time', 'author' => 'Stephen Hawking', 'description' => 'An explanation of cosmology.'],
            ['title' => 'Clean Code', 'author' => 'Robert C. Martin', 'description' => 'A handbook of Agile software craftsmanship.'],
            ['title' => 'The Hobbit', 'author' => 'J.R.R. Tolkien', 'description' => 'A fantasy novel before Lord of the Rings.']
        ];

        foreach ($books as $bookData) {
            $book = Book::create($bookData);

            $categoryIds = Category::inRandomOrder()->take(rand(1, 3))->pluck('id');
            $book->categories()->attach($categoryIds);
        }
    }
}
