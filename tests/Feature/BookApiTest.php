<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    use DatabaseMigrations;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_get_all_books()
    {
        $response = $this->getJson('/api/v1/books');

        $response
            ->assertOk()
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
                        'prev',
                        'next',
                    ],
                ],
            ])
            ->assertJsonCount(15, 'data.items');
    }

    public function test_get_books_search()
    {
        $response = $this->getJson('/api/v1/books?search=ipsum');

        $response
            ->assertOk()
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
                        'prev',
                        'next',
                    ],
                ],
            ]);
    }

    public function test_get_books_search_validation_error()
    {
        $blankSearch = $this->getJson('/api/v1/books?search=');
        $blankSearch
            ->assertUnprocessable()
            ->assertInvalid([
                'search' => 'The search field must be a string.',
            ])
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'search',
                ],
            ]);

        $invalidSearch = $this->getJson('/api/v1/books?search=min');
        $invalidSearch
            ->assertUnprocessable()
            ->assertInvalid([
                'search' => 'The search field must be at least 5 characters.',
            ])
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'search',
                ],
            ]);

        $longSearch = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec';
        $invalidSearch = $this->getJson("/api/v1/books?search=$longSearch");
        $invalidSearch
            ->assertUnprocessable()
            ->assertInvalid([
                'search' => 'The search field must not be greater than 20 characters.',
            ])
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'search',
                ],
            ]);
    }

    public function test_get_books_search_not_found()
    {
        $response = $this->getJson('/api/v1/books?search=Not Found');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'items' => [],
                    'links' => [
                        'prev',
                        'next',
                    ],
                ],
            ])
            ->assertJsonCount(0, 'data.items');
    }

    public function test_get_books_paginated()
    {
        $response = $this->getJson('/api/v1/books?perPage=5&columns[]=id&columns[]=title');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'items' => [
                        '*' => [
                            'id',
                            'title',
                        ],
                    ],
                    'links' => [
                        'prev',
                        'next',
                    ],
                ],
            ])
            ->assertJsonCount(5, 'data.items');
    }

    public function test_store_book()
    {
        $response = $this->postJson('/api/v1/books', [
            'title' => $this->faker->name,
            'description' => $this->faker->sentence,
            'publish_date' => $this->faker->date,
            'author_id' => 1,
        ]);

        $response
            ->assertCreated()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'title',
                    'description',
                    'publish_date',
                    'author_id',
                    'created_at',
                    'updated_at',
                ],
                'message',
            ]);
    }

    public function test_store_book_validation_error()
    {
        $response = $this->postJson('/api/v1/books', [
            'title' => '',
            'description' => '',
            'publish_date' => '01-01-2000',
            'author_id' => 5000,
        ]);

        $response
            ->assertUnprocessable()
            ->assertInvalid([
                'title' => 'The title field is required.',
                'publish_date' => 'The publish date field must match the format Y-m-d.',
                'author_id' => 'The selected author id is invalid.',
            ])
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'title',
                    'publish_date',
                    'author_id',
                ],
            ]);
    }

    public function test_show_book()
    {
        $response = $this->getJson('/api/v1/books/1');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'title',
                    'description',
                    'publish_date',
                    'author' => [
                        'id',
                        'name',
                        'bio',
                        'birth_date',
                        'created_at',
                        'updated_at',
                    ],
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    public function test_show_book_not_found()
    {
        $response = $this->getJson('/api/v1/books/1000');

        $response
            ->assertNotFound()
            ->assertJsonStructure([
                'message',
            ])
            ->assertJsonPath('message', 'Book not found');
    }

    public function test_update_book()
    {
        $response = $this->putJson('/api/v1/books/1', [
            'title' => $this->faker->name,
            'description' => $this->faker->sentence,
            'publish_date' => $this->faker->date,
            'author_id' => 1,
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'title',
                    'description',
                    'publish_date',
                    'author_id',
                    'created_at',
                    'updated_at',
                ],
                'message',
            ]);
    }

    public function test_update_book_not_found()
    {
        $response = $this->putJson('/api/v1/books/1000', [
            'title' => $this->faker->name,
            'description' => $this->faker->sentence,
            'publish_date' => $this->faker->date,
            'author_id' => 1,
        ]);

        $response
            ->assertNotFound()
            ->assertJsonStructure([
                'message',
            ])
            ->assertJsonPath('message', 'Book not found');
    }

    public function test_update_book_validation_error()
    {
        $response = $this->putJson('/api/v1/books/2', [
            'title' => '',
            'description' => '',
            'publish_date' => '01-01-2000',
            'author_id' => 5000,
        ]);

        $response
            ->assertUnprocessable()
            ->assertInvalid([
                'title' => 'The title field is required.',
                'publish_date' => 'The publish date field must match the format Y-m-d.',
                'author_id' => 'The selected author id is invalid.',
            ])
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'title',
                    'publish_date',
                    'author_id',
                ],
            ]);
    }

    public function test_delete_book()
    {
        $response = $this->deleteJson('/api/v1/books/3');

        $response->assertNoContent();
    }

    public function test_delete_book_not_found()
    {
        $response = $this->deleteJson('/api/v1/books/1000');

        $response
            ->assertNotFound()
            ->assertJsonStructure([
                'message',
            ])
            ->assertJsonPath('message', 'Book not found');
    }
}
