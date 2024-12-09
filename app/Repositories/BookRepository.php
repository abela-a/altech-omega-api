<?php

namespace App\Repositories;

use App\Interfaces\BookRepositoryInterface;
use App\Models\Book;

class BookRepository implements BookRepositoryInterface
{
    public function index($query)
    {
        $title = $query['title'] ?? null;
        $publishDate = $query['publish_date'] ?? null;

        return Book::query()
            ->when($title, fn ($query, $value) => $query->where('title', 'LIKE', "%$value%"))
            ->when($publishDate, fn ($query, $value) => $query->where('publish_date', $value))
            ->paginate();
    }

    public function show($id)
    {
        return Book::with('author')->findOrFail($id);
    }

    public function store(array $data)
    {
        return Book::create($data);
    }

    public function update(array $data, $id)
    {
        $book = Book::findOrFail($id);
        $book->update($data);

        return $book;
    }

    public function delete($id)
    {
        Book::destroy($id);
    }

    public function authorBooks($id)
    {
        return Book::whereAuthorId($id)->paginate();
    }
}
