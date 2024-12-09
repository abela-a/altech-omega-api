<?php

namespace Database\Seeders;

use Database\Factories\AuthorFactory;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AuthorFactory::new()->count(50)->create();
    }
}
