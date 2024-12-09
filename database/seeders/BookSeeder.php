<?php

namespace Database\Seeders;

use Database\Factories\BookFactory;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BookFactory::new()->count(500)->create();
    }
}
