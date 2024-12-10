<?php

namespace App\Repositories;

use App\Interfaces\BookRepositoryInterface;
use App\Models\Book;
use Illuminate\Support\Facades\Cache;

class BookRepository implements BookRepositoryInterface
{
    public function index($query)
    {
        $params = [
            'search' => $query['search'] ?? null,
            'publishDate' => $query['publish_date'] ?? null,
            'paginate' => [
                'perPage' => $query['perPage'] ?? 15,
                'columns' => $query['columns'] ?? ['*'],
            ],
        ];

        $cacheKey = 'books:index|'.serialize($query);

        return Cache::remember($cacheKey, 60, function () use ($params) {
            return Book::query()
                ->search($params['search'])
                ->publishedAt($params['publishDate'])
                ->cursorPaginate(
                    $params['paginate']['perPage'],
                    $params['paginate']['columns'],
                );
        });

    }

    public function show($id)
    {
        return Cache::remember('books:'.$id, 60, function () use ($id) {
            return Book::with('author')->findOrFail($id);
        });
    }

    public function store(array $data)
    {
        return Book::create($data);
    }

    public function update(array $data, $id)
    {
        $book = Book::findOrFail($id);
        $book->update($data);

        Cache::forget('books:'.$id);

        return $book;
    }

    public function delete($id)
    {
        Book::destroy($id);
        Cache::forget('books:'.$id);
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
