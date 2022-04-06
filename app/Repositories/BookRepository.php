<?php

namespace App\Repositories;

use App\Interfaces\BookRepositoryInterface;
use App\Models\Book;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class BookRepository implements BookRepositoryInterface {
    const DEFAULT_LIMIT = 0;
    const EXPLODE_SYMBOL = ',';
    const SEPARATOR_SYMBOL = ':';

    use Searchable;

    public function search(array $queryParams): Collection|array
    {
        return $this->find($queryParams, Book::class);
    }


    public function create(array $attributes): Book {
        $book = Book::query()->create($attributes);
        if(isset($attributes['authors'])) {
            $authors = Arr::pluck($attributes['authors'], 'id');
            $book->authors()->attach($authors);
        }
        return Book::findOrFail($book->id);
    }

    public function get(int $id): Book {
        return Book::findOrFail($id);
    }

    public function update(array $attributes, int $id): Book {
        $book = Book::findOrFail($id);
        $book->update($attributes);

        if(isset($attributes['del_authors'])) {
            $authors = Arr::pluck($attributes['del_authors'], 'id');
            $book->authors()->detach($authors);
        }

        if(isset($attributes['add_authors'])) {
            $authors = Arr::pluck($attributes['add_authors'], 'id');
            $book->authors()->attach($authors);
        }

        return Book::findOrFail($book->id);
    }

    public function delete(int $id): void {
        Book::findOrFail($id)->deleteOrFail();
    }

}
