<?php

namespace App\Repositories;

use App\Interfaces\AuthorRepositoryInterface;
use App\Models\Author;

class AuthorRepository implements AuthorRepositoryInterface
{
    public function index($query)
    {
        $name = $query['name'] ?? null;

        return Author::query()
            ->when($name, fn ($query, $value) => $query->where('name', 'LIKE', "%$value%"))
            ->paginate();
    }

    public function show($id)
    {
        return Author::findOrFail($id);
    }

    public function store(array $data)
    {
        return Author::create($data);
    }

    public function update(array $data, $id)
    {
        $author = Author::findOrFail($id);
        $author->update($data);

        return $author;
    }

    public function delete($id)
    {
        Author::destroy($id);
    }
}
