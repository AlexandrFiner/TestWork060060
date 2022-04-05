<?php

namespace App\Repositories;

use App\Interfaces\BookRepositoryInterface;
use App\Models\Book;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class BookRepository implements BookRepositoryInterface {
    const DEFAULT_LIMIT = 0;
    const EXPLODE_SYMBOL = ',';
    const SEPARATOR_SYMBOL = ':';

    public function search(array $queryParams): Collection|array
    {
        $limit = self::DEFAULT_LIMIT;

        $books = Book::query();

        foreach ($queryParams as $param => $value) {
            if(empty($value))
                continue;

            if($param === 'limit') {
                $limit = (int) $value;
                continue;
            }

            switch ($param) {
                case "id":
                    $books->where($param, $value);
                    break;

                case "title":
                    $books->where($param, 'like', '%'.$value.'%');
                    break;

                case "author_id":
                    $books->whereHas('authors', fn($query) => $query->where('authors.id', $value));
                    break;

                case "author_name":
                    $books->whereHas('authors', fn($query) => $query->where('authors.name', 'like', '%'.$value.'%'));
                    break;

                case "sort":
                    $sorters = explode(self::EXPLODE_SYMBOL, $value);
                    foreach($sorters as $sorter) {
                        $data = explode(self::SEPARATOR_SYMBOL, $sorter);
                        if(!in_array($data[0], ['id', 'title', 'rating']))
                            continue;

                        if(!isset($data[1]))
                            $data[1] = 'desc';

                        $books->orderBy($data[0], ($data[1] == 'desc' ? 'desc' : 'asc'));
                    }
                    break;
            }
        }

        if($limit)
            $books->take($limit);

        return $books->get();
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
