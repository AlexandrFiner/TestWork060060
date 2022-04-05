<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Author::factory(4)->create();
        foreach (Book::all() as $book) {
            $authors = Author::all()->random(rand(1,3));

            $book->authors()
                ->attach($authors);
        }
    }
}
