<?php

namespace App\Repositories;

use App\Interfaces\AuthorRepositoryInterface;
use App\Models\Author;

class AuthorRepository implements AuthorRepositoryInterface
{
    public function index()
    {
        return Author::paginate();
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
