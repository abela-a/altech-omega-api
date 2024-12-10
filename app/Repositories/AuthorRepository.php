<?php

namespace App\Repositories;

use App\Interfaces\AuthorRepositoryInterface;
use App\Models\Author;
use Illuminate\Support\Facades\Cache;

class AuthorRepository implements AuthorRepositoryInterface
{
    public function index($query)
    {
        $params = [
            'name' => $query['name'] ?? null,
            'paginate' => [
                'perPage' => $query['perPage'] ?? 15,
                'columns' => $query['columns'] ?? ['*'],
                'pageName' => $query['pageName'] ?? 'page',
                'page' => $query['page'] ?? null,
            ],
        ];

        $cacheKey = 'authors:index|'.serialize($params);

        return Cache::remember($cacheKey, 60, function () use ($params) {
            return Author::query()
                ->name($params['name'])
                ->simplePaginate(
                    $params['paginate']['perPage'],
                    $params['paginate']['columns'],
                    $params['paginate']['pageName'],
                    $params['paginate']['page']
                );
        });
    }

    public function show($id)
    {
        return Cache::remember('authors:'.$id, 60, function () use ($id) {
            return Author::findOrFail($id);
        });
    }

    public function store(array $data)
    {
        return Author::create($data);
    }

    public function update(array $data, $id)
    {
        $author = Author::findOrFail($id);
        $author->update($data);

        Cache::forget('authors:'.$id);

        return $author;
    }

    public function delete($id)
    {
        Author::destroy($id);
        Cache::forget('authors:'.$id);
    }
}
