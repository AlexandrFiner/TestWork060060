<?php

namespace App\Interfaces;

use App\Models\Book;
use Illuminate\Database\Eloquent\Collection;

interface BookRepositoryInterface {
    public function search(array $queryParams): Collection|array;
    public function create(array $attributes): Book;
    public function get(int $id): Book;
    public function update(array $attributes, int $id): Book;
    public function delete(int $id): void;
}
