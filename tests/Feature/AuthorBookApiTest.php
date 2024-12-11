<?php

namespace Tests\Feature;

use Database\Seeders\AuthorSeeder;
use Database\Seeders\BookSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthorBookApiTest extends TestCase
{
    use DatabaseMigrations;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AuthorSeeder::class);
        $this->seed(BookSeeder::class);
    }

    public function test_get_all_author_books()
    {
        $response = $this->getJson('/api/v1/authors/1/books');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'items' => [
                        '*' => [
                            'id',
                            'title',
                            'description',
                            'publish_date',
                            'author_id',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                    'links' => [
                        'first',
                        'last',
                        'prev',
                        'next',
                    ],
                    'meta' => [
                        'current_page',
                        'from',
                        'last_page',
                        'path',
                        'per_page',
                        'to',
                        'total',
                    ],
                ],
            ]);
    }

    public function test_get_all_author_books_paginated()
    {
        $response = $this->getJson('/api/v1/authors/1/books?page=2');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'items' => [
                        '*' => [
                            'id',
                            'title',
                            'description',
                            'publish_date',
                            'author_id',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                    'links' => [
                        'first',
                        'last',
                        'prev',
                        'next',
                    ],
                    'meta' => [
                        'current_page',
                        'from',
                        'last_page',
                        'path',
                        'per_page',
                        'to',
                        'total',
                    ],
                ],
            ])
            ->assertJsonPath('data.meta.current_page', 2);
    }
}
