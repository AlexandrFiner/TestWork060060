<?php

namespace App\Traits;

use App\Models\Book;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

const EXPLODE_SYMBOL = ',';
const SEPARATOR_SYMBOL = ':';

const SORT_COLUMN = 0;
const SORT_DIRECTION = 1;

Trait Searchable {

    protected function getWhereIds(Builder $query, array $queryParams): Builder
    {
        if(!isset($queryParams['ids']))
            return $query;

        $ids = explode(EXPLODE_SYMBOL, $queryParams['ids']);
        $query->whereIn('id', $ids);
        return $query;
    }

    protected function getSort(Builder $query, array $queryParams): Builder
    {
        if(!isset($queryParams['sort']))
            return $query;

        $sorters = explode(EXPLODE_SYMBOL, $queryParams['sort']);
        foreach($sorters as $sorter) {
            $data = explode(SEPARATOR_SYMBOL, $sorter);

            if(!isset($data[SORT_DIRECTION]))
                $data[SORT_DIRECTION] = 'desc';

            $query->orderBy($data[SORT_COLUMN], ($data[SORT_DIRECTION] == 'desc' ? 'desc' : 'asc'));
        }
        return $query;
    }

    protected function getLimit(array $queryParams) {
        return $queryParams['limit'] ?? -1;
    }

    protected function find(array $queryParams, string $modelName): Collection {

        $model = $modelName::query();

        /*
         * TODO: Не успел чутка доработать, все это тоже нужно нормально разбить
         */
        foreach ($queryParams as $param => $value) {
            if(empty($value))
                continue;

            switch ($param) {
                case "title":
                    $model->where($param, 'like', '%'.$value.'%');
                    break;

                case "author_ids":
                    $authors = explode(EXPLODE_SYMBOL, $value);
                    foreach($authors as $authorId)
                        $model->whereHas('authors', fn(Builder $query) => $query->where('authors.id', $authorId));

                    break;

                case "author_name":
                    $model->whereHas('authors', fn(Builder $query) => $query->where('authors.name', 'like', '%'.$value.'%'));
                    break;
            }
        }

        $this->getWhereIds($model, $queryParams);
        $this->getSort($model, $queryParams);
        $model->limit($this->getLimit($queryParams));

        return $model->get();
    }
}
