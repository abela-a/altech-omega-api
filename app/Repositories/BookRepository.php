<?php

namespace App\Repositories;

use App\Interfaces\BookRepositoryInterface;
use App\Models\Book;

class BookRepository implements BookRepositoryInterface
{
    public function index()
    {
        return Book::paginate();
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
