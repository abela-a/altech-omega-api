<?php

namespace App\Repositories;

use App\Interfaces\BookRepositoryInterface;
use App\Models\Book;
use Illuminate\Support\Facades\Cache;

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

    public function authorBooks($id, $query)
    {
        $params = [
            'paginate' => [
                'perPage' => $query['perPage'] ?? 15,
                'columns' => $query['columns'] ?? ['*'],
                'pageName' => $query['pageName'] ?? 'page',
                'page' => $query['page'] ?? null,
                'total' => $query['total'] ?? null,
            ],
        ];

        $cacheKey = 'books:author:'.$id.'|'.serialize($query);

        return Cache::remember($cacheKey, 60, function () use ($id, $params) {
            return Book::whereAuthorId($id)->paginate(
                $params['paginate']['perPage'],
                $params['paginate']['columns'],
                $params['paginate']['pageName'],
                $params['paginate']['page'],
                $params['paginate']['total'],
            );
        });
    }
}
