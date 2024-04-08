<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Random\RandomException;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * @throws RandomException
     */
    public function run(): void
    {
        Book::factory(100)->create()->each(function ($book) {
            $numberOfReviews = random_int(5, 30);
            Review::factory($numberOfReviews)->for($book)->create();
        });
    }
}
